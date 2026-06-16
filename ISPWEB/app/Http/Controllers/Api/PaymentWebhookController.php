<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Karim007\SslcommerzLaravel\SslCommerz\SslCommerzNotification;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;

class PaymentWebhookController extends Controller
{
    /**
     * Handle SSLCommerz IPN (Instant Payment Notification)
     */
    public function sslcommerzIpn(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();
        $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

        if ($validation) {
            return $this->processSuccessfulPayment($tran_id, 'SSLCommerz');
        }

        Log::error("SSLCommerz IPN Validation Failed for Transaction: $tran_id");
        return response()->json(['message' => 'Payment validation failed'], 400);
    }

    /**
     * Handle bKash Webhook / Callback
     */
    public function bkashCallback(Request $request)
    {
        $paymentID = $request->paymentID;
        
        // Execute the payment
        $response = BkashPaymentTokenize::executePayment($paymentID);

        if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
            // bKash uses merchantInvoiceNumber which we set during createPayment
            $invoiceNumber = $response['merchantInvoiceNumber'];
            return $this->processSuccessfulPayment($invoiceNumber, 'bKash');
        }

        Log::error("bKash Payment Failed or Cancelled: " . json_encode($response));
        return response()->json(['message' => 'Payment failed or cancelled'], 400);
    }

    /**
     * Core logic to process successful payments and activate users
     */
    protected function processSuccessfulPayment($invoiceNumber, $gateway)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        
        if (!$invoice) {
            Log::error("PaymentWebhook: Invoice not found - $invoiceNumber");
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        if ($invoice->payment_status === 'paid') {
            return response()->json(['message' => 'Invoice already paid']);
        }

        // Update Invoice
        $invoice->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $gateway // Assuming you might have added this column, or you can skip it
        ]);

        // Update Customer Profile
        $profile = CustomerProfile::where('user_id', $invoice->customer_id)->first();
        if ($profile) {
            // Add 30 days to expiry_date
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
                
                if ($profile->package) {
                    $mikrotik->changeProfile($profile->pppoe_username, $profile->package->mikrotik_profile_name);
                }
                
                $mikrotik->enablePppSecret($profile->pppoe_username);
                $mikrotik->disconnectActiveSession($profile->pppoe_username);

                Log::info("Auto-Provisioning Success for user: {$profile->pppoe_username} via $gateway");

            } catch (\Exception $e) {
                Log::error("Mikrotik Provisioning Error: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Payment processed successfully', 'gateway' => $gateway]);
    }
}
