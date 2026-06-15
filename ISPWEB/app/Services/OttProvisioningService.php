<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OttProvisioningService
{
    /**
     * Activate OTT subscriptions based on package inclusions
     */
    public function provisionOttBundle($customerId, $phone, $package)
    {
        if (empty($package->included_otts)) {
            return;
        }

        $providers = json_decode($package->included_otts, true);
        if (!is_array($providers)) return;

        foreach ($providers as $provider) {
            $this->activateProvider($customerId, $phone, strtolower($provider));
        }
    }

    private function activateProvider($customerId, $phone, $provider)
    {
        try {
            // Mocking Third-Party Partner API Calls (Chorki, Hoichoi, etc)
            $success = false;

            if ($provider === 'chorki') {
                // $client->post('https://api.chorki.com/v1/partner/activate', [...])
                $success = true;
            } elseif ($provider === 'hoichoi') {
                // $client->post('https://api.hoichoi.tv/partner/provision', [...])
                $success = true;
            } elseif ($provider === 'bongo') {
                $success = true;
            }

            if ($success) {
                DB::table('ott_subscriptions')->updateOrInsert(
                    ['customer_id' => $customerId, 'provider' => $provider],
                    [
                        'phone_number' => $phone,
                        'status' => 'active',
                        'expires_at' => Carbon::now()->addDays(30),
                        'updated_at' => now()
                    ]
                );
                Log::info("OTT Provisioned: {$provider} for {$phone}");
            }

        } catch (\Exception $e) {
            Log::error("OTT Provisioning Failed for {$provider}: " . $e->getMessage());
        }
    }
}
