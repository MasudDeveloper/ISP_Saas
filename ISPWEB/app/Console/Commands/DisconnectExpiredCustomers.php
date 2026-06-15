<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;

class DisconnectExpiredCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'isp:disconnect-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds expired customers, disables their PPP secret and disconnects them from Mikrotik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting expired customers check...");

        // Fetch all profiles that are Active but their expiry date is in the past
        CustomerProfile::where('status', 'Active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->chunk(100, function ($profiles) {
                foreach ($profiles as $profile) {
                    $this->processProfile($profile);
                }
            });

        $this->info("Completed expired customers check.");
    }

    private function processProfile(CustomerProfile $profile)
    {
        try {
            // Update Database first
            $profile->update(['status' => 'Expired']);
            
            // Connect to Mikrotik
            $mikrotik = new MikrotikService($profile->router);
            
            // 1. Disable the secret
            $mikrotik->disablePppSecret($profile->pppoe_username);
            
            // 2. Disconnect active session (so they get disconnected immediately)
            $mikrotik->disconnectActiveSession($profile->pppoe_username);
            
            Log::info("Disconnected expired user: {$profile->pppoe_username}");
            $this->line("Disconnected: {$profile->pppoe_username}");

        } catch (\Exception $e) {
            Log::error("Failed to disconnect user {$profile->pppoe_username}: " . $e->getMessage());
            $this->error("Error for {$profile->pppoe_username}: " . $e->getMessage());
        }
    }
}
