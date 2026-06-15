<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GracePeriodController extends Controller
{
    /**
     * Give 24 hours emergency grace period
     */
    public function requestGracePeriod(Request $request)
    {
        // Require auth
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $profile = CustomerProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        if ($profile->status !== 'Expired') {
            return response()->json(['error' => 'Grace period is only available for expired accounts'], 400);
        }

        // Limit grace period usage (e.g., max once per 30 days)
        if ($profile->grace_period_used_at && Carbon::parse($profile->grace_period_used_at)->diffInDays(now()) < 30) {
            return response()->json(['error' => 'You have already used your emergency balance this month. Please pay your bill.'], 403);
        }

        // Logic: Give 24 hours from NOW
        $profile->update([
            'status' => 'Active',
            'expiry_date' => now()->addHours(24),
            'grace_period_used_at' => now()
        ]);

        // Re-enable in Mikrotik
        try {
            $mikrotik = new MikrotikService($profile->router);
            $mikrotik->enablePppSecret($profile->pppoe_username);
            $mikrotik->disconnectActiveSession($profile->pppoe_username); // drop to apply

            Log::info("Grace period granted for: {$profile->pppoe_username}");
            
            return response()->json(['message' => 'Emergency 24-hour internet activated successfully!']);
        } catch (\Exception $e) {
            Log::error("Grace Period Mikrotik Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to activate in router. Contact support.'], 500);
        }
    }
}
