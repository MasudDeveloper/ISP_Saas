<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle the incoming payment gateway webhook
     */
    public function handle(Request $request)
    {
        // TODO: Verify Webhook Signature based on your Payment Gateway (e.g. bKash/SSLCommerz/Stripe)
        $secretKey = env('WEBHOOK_SECRET', 'my_secret_token_123');
        if ($request->header('X-Webhook-Secret') !== $secretKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate basic payload
        $request->validate([
            'invoice_number' => 'required|string',
            'status' => 'required|string', // e.g., 'paid', 'completed'
        ]);

        if (in_array(strtolower($request->status), ['paid', 'completed', 'successful'])) {
            $invoice = Invoice::where('invoice_number', $request->invoice_number)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            if ($invoice->payment_status === 'paid') {
                return response()->json(['message' => 'Invoice already paid']);
            }

            // Update Invoice
            $invoice->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Update Customer Profile
            $profile = CustomerProfile::where('user_id', $invoice->customer_id)->first();
            if ($profile) {
                // Add 30 days to expiry_date (from now if expired, or from existing expiry if still active)
                if ($profile->status === 'Expired' || $profile->expiry_date < now()) {
                    $newExpiry = now()->addDays(30);
                } else {
                    $newExpiry = Carbon::parse($profile->expiry_date)->addDays(30);
                }

                $profile->update([
                    'status' => 'Active',
                    'expiry_date' => $newExpiry,
                    'package_id' => $invoice->package_id ?? $profile->package_id,
                ]);

                // Mikrotik Auto-Provisioning
                try {
                    $mikrotik = new MikrotikService($profile->router);
                    
                    // Restore profile if changed
                    if ($profile->package) {
                        $mikrotik->changeProfile($profile->pppoe_username, $profile->package->mikrotik_profile_name);
                    }
                    
                    // Enable Secret
                    $mikrotik->enablePppSecret($profile->pppoe_username);
                    
                    // If they were active but limited, disconnect to force reconnect with new speed
                    $mikrotik->disconnectActiveSession($profile->pppoe_username);

                    Log::info("Auto-Provisioning Success for user: {$profile->pppoe_username}");

                } catch (\Exception $e) {
                    Log::error("Mikrotik Provisioning Error: " . $e->getMessage());
                    // Don't fail the webhook, log it for admin manual fix or retry job
                }
            }

            return response()->json(['message' => 'Payment processed and line activated']);
        }

        return response()->json(['message' => 'Payment not successful, ignoring.']);
    }
}
