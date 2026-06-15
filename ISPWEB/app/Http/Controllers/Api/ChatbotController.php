<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendWhatsAppAlert;

class ChatbotController extends Controller
{
    /**
     * Webhook verification for Meta/WhatsApp
     */
    public function verifyWebhook(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'isp_bot_123');
        
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
            return response('Forbidden', 403);
        }
        return response('Bad Request', 400);
    }

    /**
     * Handle incoming WhatsApp Messages
     */
    public function handleMessage(Request $request)
    {
        $data = $request->all();

        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $messageObj = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $phone = $messageObj['from']; // Senders phone number
            $text = strtolower(trim($messageObj['text']['body'] ?? ''));

            // Remove country code for DB search (assuming BD +880 or similar logic, keeping simple here)
            // Ideally we search by partial phone match or exact match depending on how it's stored.
            
            // 1. Find User by Phone (Assuming phone is stored in 'name' or separate column. 
            // For this architecture, let's assume we find by email or we have a phone column. 
            // We'll mock the find logic).
            // FIXME: Ensure 'phone' column exists or search in profiles
            $user = User::where('email', 'like', "%{$phone}%")->first(); // Placeholder logic

            if (!$user) {
                SendWhatsAppAlert::dispatch($phone, "Welcome to ISP SaaS. We couldn't find your account. Please reply with your User ID.");
                return response()->json(['status' => 'ok']);
            }

            $profile = CustomerProfile::where('user_id', $user->id)->first();

            if (in_array($text, ['hi', 'hello', 'menu'])) {
                $reply = "Hello {$user->name}! How can we help you today?\n1. Internet not working\n2. View Bill\n3. Talk to Agent";
                SendWhatsAppAlert::dispatch($phone, $reply);
                return response()->json(['status' => 'ok']);
            }

            if ($text == '1' || str_contains($text, 'not working')) {
                // Self-Healing Logic
                if ($profile && $profile->status === 'Expired') {
                    // Check Unpaid Bills
                    $pendingInvoice = Invoice::where('customer_id', $user->id)->where('payment_status', 'unpaid')->first();
                    if ($pendingInvoice) {
                        $reply = "Your account is currently expired due to an unpaid bill of {$pendingInvoice->amount} BDT. Please pay using this link: https://portal.local/pay/{$pendingInvoice->id}";
                    } else {
                        $reply = "Your account is expired. Please renew your package.";
                    }
                } else {
                    // Line is active, but not working. Auto Open Ticket.
                    $ticketId = "TKT-" . rand(1000, 9999);
                    
                    DB::table('tickets')->insert([
                        'customer_id' => $user->id,
                        'issue_type' => 'No Internet (Auto-Detected)',
                        'status' => 'Open',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $reply = "We have automatically created a support ticket ({$ticketId}) for your line. Our technical team is checking the router status. Please wait.";
                }

                SendWhatsAppAlert::dispatch($phone, $reply);
            }

            if ($text == '2' || str_contains($text, 'bill')) {
                $pendingInvoice = Invoice::where('customer_id', $user->id)->where('payment_status', 'unpaid')->first();
                if ($pendingInvoice) {
                    $reply = "Your current due is {$pendingInvoice->amount} BDT. Due Date: " . \Carbon\Carbon::parse($pendingInvoice->due_date)->format('d M Y');
                } else {
                    $reply = "You have no pending bills. Your account is fully paid.";
                }
                SendWhatsAppAlert::dispatch($phone, $reply);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
