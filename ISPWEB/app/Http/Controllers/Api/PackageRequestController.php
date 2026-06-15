<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Models\InternetPackage;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Services\MikrotikService;
use Carbon\Carbon;

class PackageRequestController extends Controller
{
    /**
     * Customer requests a package upgrade/downgrade from Android App
     */
    public function requestChange(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'requested_package_id' => 'required|exists:internet_packages,id'
        ]);

        $profile = CustomerProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        if ($profile->package_id == $request->requested_package_id) {
            return response()->json(['error' => 'You are already on this package'], 400);
        }

        // Create Pending Request
        DB::table('package_change_requests')->insert([
            'customer_id' => $user->id,
            'current_package_id' => $profile->package_id,
            'requested_package_id' => $request->requested_package_id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Request submitted. Waiting for admin approval.']);
    }

    /**
     * Admin Approves the request (Pro-Rata Calculation)
     */
    public function approveChange(Request $request, $id)
    {
        // Must be admin check here...
        
        $changeRequest = DB::table('package_change_requests')->where('id', $id)->first();
        if (!$changeRequest || $changeRequest->status !== 'pending') {
            return response()->json(['error' => 'Invalid or already processed request'], 400);
        }

        $profile = CustomerProfile::where('user_id', $changeRequest->customer_id)->first();
        $oldPackage = InternetPackage::find($changeRequest->current_package_id);
        $newPackage = InternetPackage::find($changeRequest->requested_package_id);

        if (!$profile || !$oldPackage || !$newPackage) {
            return response()->json(['error' => 'Data inconsistency found'], 500);
        }

        // === PRO-RATA CALCULATION ===
        $now = now();
        $expiryDate = Carbon::parse($profile->expiry_date);
        
        if ($profile->status === 'Active' && $expiryDate->isFuture()) {
            $daysRemaining = $now->diffInDays($expiryDate);
            $totalDaysInMonth = 30; // Assuming 30 days billing cycle

            // Value of remaining days on old package
            $oldDailyRate = $oldPackage->price / $totalDaysInMonth;
            $unusedBalance = $oldDailyRate * $daysRemaining;

            // Cost of remaining days on new package
            $newDailyRate = $newPackage->price / $totalDaysInMonth;
            $costForRemainingDays = $newDailyRate * $daysRemaining;

            $adjustmentAmount = $costForRemainingDays - $unusedBalance;

            if ($adjustmentAmount > 0) {
                // Customer needs to pay extra for the remaining days
                Invoice::create([
                    'customer_id' => $changeRequest->customer_id,
                    'amount' => round($adjustmentAmount, 2),
                    'payment_status' => 'unpaid',
                    'due_date' => $now->addDays(2), // 2 days to pay the adjustment
                ]);
            }
        }

        // Apply changes to database
        $profile->update(['package_id' => $newPackage->id]);

        DB::table('package_change_requests')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);

        // Push to Mikrotik
        try {
            $mikrotik = new MikrotikService($profile->router);
            $mikrotik->changeProfile($profile->pppoe_username, $newPackage->mikrotik_profile_name);
            $mikrotik->disconnectActiveSession($profile->pppoe_username); // Force reconnect for new speed
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Pro-Rata Mikrotik Error: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Package upgraded and pro-rata invoice generated']);
    }
}
