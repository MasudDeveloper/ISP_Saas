<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = env('WHATSAPP_TOKEN', '');
        
        if (empty($token) || empty($this->phone)) {
            Log::warning("WhatsApp API: Token or Phone missing");
            return;
        }

        // WhatsApp Cloud API Call Simulation
        $url = "https://graph.facebook.com/v17.0/YOUR_PHONE_NUMBER_ID/messages";
        
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->phone,
            'type' => 'text',
            'text' => ['body' => $this->message]
        ];

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            Log::error("WhatsApp Error: " . curl_error($ch));
        } else {
            Log::info("WhatsApp Alert Sent to {$this->phone}");
        }
        
        curl_close($ch);
    }
}
