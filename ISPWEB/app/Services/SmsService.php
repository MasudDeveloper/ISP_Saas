<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using AlphaSMS / BD Bulk SMS Gateway
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function sendSms($phone, $message)
    {
        $apiUrl = env('SMS_API_URL', 'https://api.smsq.global/api/v2/SendSMS');
        $apiKey = env('SMS_API_KEY');
        $senderId = env('SMS_SENDER_ID');

        if (!$apiKey) {
            Log::warning("SMS API Key not configured. Skipping SMS to $phone");
            return false;
        }

        try {
            $response = Http::get($apiUrl, [
                'ApiKey' => $apiKey,
                'ClientId' => env('SMS_CLIENT_ID'), // If required by provider
                'SenderId' => $senderId,
                'Message' => $message,
                'MobileNumbers' => $phone
            ]);

            if ($response->successful()) {
                Log::info("SMS sent to $phone. Response: " . $response->body());
                return true;
            } else {
                Log::error("Failed to send SMS to $phone. Status: " . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS Gateway Exception: " . $e->getMessage());
            return false;
        }
    }
}
