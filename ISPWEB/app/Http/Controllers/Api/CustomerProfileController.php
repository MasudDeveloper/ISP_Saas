<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class CustomerProfileController extends Controller
{
    /**
     * Get Customer Profile and Active Plan
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        $profile = CustomerProfile::with('package')->where('user_id', $user->id)->first();
        
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $profile->pppoe_username, // Just as an example, adjust as per DB
            'status' => $profile->status,
            'expiry_date' => $profile->expiry_date,
            'active_plan' => $profile->package ? $profile->package->name : 'N/A',
            'plan_speed' => $profile->package ? $profile->package->speed_mbps . ' Mbps' : 'N/A',
            'monthly_fee' => $profile->package ? $profile->package->price : 0,
        ]);
    }

    /**
     * Get Usage History (Data from BTRC Logs)
     */
    public function getUsageHistory(Request $request)
    {
        $user = $request->user();
        $profile = CustomerProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // Fetch last 30 days usage
        $usage = DB::table('btrc_logs')
            ->select(DB::raw('DATE(session_start) as date'), DB::raw('SUM(bytes_down) as total_download'), DB::raw('SUM(bytes_up) as total_upload'))
            ->where('username', $profile->pppoe_username)
            ->where('session_start', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'usage_history' => $usage
        ]);
    }

    /**
     * Get Billing History
     */
    public function getBillingHistory(Request $request)
    {
        $user = $request->user();
        
        $invoices = Invoice::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'invoices' => $invoices
        ]);
    }
}
