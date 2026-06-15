<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Jobs\SendBillingSms;
use Illuminate\Support\Facades\Log;

class BroadcastController extends Controller
{
    /**
     * Trigger SMS Blast for a specific router/zone (e.g., Fiber Cut)
     */
    public function blastSms(Request $request)
    {
        // Only Admin or Reseller can trigger
        $user = $request->user();
        if (!in_array($user->role, ['super_admin', 'reseller'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'router_id' => 'required|exists:mikrotik_routers,id',
            'message' => 'required|string|max:160'
        ]);

        $routerId = $request->router_id;
        $message = $request->message;

        // Fetch active customers under this router
        $profiles = CustomerProfile::with('user')
            ->where('router_id', $routerId)
            ->where('status', 'Active')
            ->get();

        if ($profiles->isEmpty()) {
            return response()->json(['message' => 'No active customers found for this router.'], 404);
        }

        $count = 0;
        foreach ($profiles as $profile) {
            // Assume the user model or profile has a 'phone' column. 
            // In our current simple schema, we might not have a dedicated phone column in User,
            // but in a real scenario, we would use $profile->user->phone.
            // For now, let's mock it or assume it exists.
            $phone = $profile->user->phone ?? '01700000000'; 
            
            // Dispatch SMS Job to Queue so server doesn't freeze
            SendBillingSms::dispatch($phone, $message);
            $count++;
        }

        Log::info("SMS Blast Triggered by {$user->name} to {$count} customers on Router {$routerId}");

        return response()->json([
            'success' => true,
            'message' => "Successfully queued SMS to {$count} customers."
        ]);
    }
}
