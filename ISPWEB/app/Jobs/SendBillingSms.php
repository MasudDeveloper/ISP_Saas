<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBillingSms implements ShouldQueue
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
        $apiKey = env('ALPHA_SMS_KEY', '');
        
        if (empty($apiKey) || empty($this->phone)) {
            Log::warning("AlphaSMS: Key or Phone missing");
            return;
        }

        // Simulating AlphaSMS API Call
        $url = "https://api.alphasms.biz/send";
        $data = [
            'api_key' => $apiKey,
            'phone' => $this->phone,
            'message' => $this->message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            Log::error("AlphaSMS Error: " . curl_error($ch));
        } else {
            Log::info("AlphaSMS Sent to {$this->phone}");
        }
        
        curl_close($ch);
    }
}
