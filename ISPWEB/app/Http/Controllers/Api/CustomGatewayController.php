<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomGatewayController extends Controller
{
    /**
     * Initiate Payment Request
     */
    public function initiatePayment(Request $request)
    {
        $user = $request->user();
        $invoiceId = $request->invoice_id;

        $invoice = Invoice::where('id', $invoiceId)->where('customer_id', $user->id)->first();
        if (!$invoice || $invoice->payment_status === 'paid') {
            return response()->json(['error' => 'Invalid or already paid invoice'], 400);
        }

        // Logic to connect to Custom Payment Gateway (e.g. bKash Tokenized API / Custom API)
        // Simulate returning a payment URL
        $paymentUrl = "https://custom-gateway.local/pay?token=" . bin2hex(random_bytes(16)) . "&amount=" . $invoice->amount;

        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl
        ]);
    }

    /**
     * Custom Gateway Webhook (Hit by your gateway in Microseconds)
     */
    public function webhook(Request $request)
    {
        $secretKey = env('CUSTOM_GATEWAY_SECRET', 'my_custom_secret');
        if ($request->header('X-Gateway-Secret') !== $secretKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'invoice_id' => 'required|integer',
            'status' => 'required|string',
            'transaction_id' => 'required|string'
        ]);

        if (strtolower($request->status) === 'success') {
            $invoice = Invoice::find($request->invoice_id);
            if (!$invoice || $invoice->payment_status === 'paid') {
                return response()->json(['message' => 'Invoice already processed'], 200);
            }

            // Update Invoice
            $invoice->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Auto-Reconnect Logic (Microsecond Level)
            $profile = CustomerProfile::where('user_id', $invoice->customer_id)->first();
            if ($profile) {
                // Add 30 Days
                $newExpiry = ($profile->status === 'Expired' || $profile->expiry_date < now()) 
                    ? now()->addDays(30) 
                    : Carbon::parse($profile->expiry_date)->addDays(30);

                $profile->update([
                    'status' => 'Active',
                    'expiry_date' => $newExpiry,
                    'grace_period_used_at' => null // Reset grace period usage on successful payment
                ]);

                // Hit Mikrotik Instantly
                try {
                    $mikrotik = new MikrotikService($profile->router);
                    $mikrotik->enablePppSecret($profile->pppoe_username);
                    $mikrotik->disconnectActiveSession($profile->pppoe_username); // Force recon
                    Log::info("Custom Gateway: Auto-reconnected {$profile->pppoe_username}");
                } catch (\Exception $e) {
                    Log::error("Custom Gateway Mikrotik Error: " . $e->getMessage());
                }

                // Push WhatsApp/SMS via Queue
                // \App\Jobs\SendWhatsAppAlert::dispatch($profile->user->phone, "Payment of {$invoice->amount} received. Your internet is active!");
            }

            return response()->json(['message' => 'Payment Success & Reconnected'], 200);
        }

        return response()->json(['message' => 'Payment Failed'], 400);
    }
}
