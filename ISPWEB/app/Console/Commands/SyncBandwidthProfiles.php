<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerProfile;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;

class SyncBandwidthProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'isp:sync-bandwidth {mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch customer bandwidth profiles between day and night mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->argument('mode'); // 'day' or 'night'
        $this->info("Starting Bandwidth Sync: {$mode} mode");

        if (!in_array($mode, ['day', 'night'])) {
            $this->error("Invalid mode. Use 'day' or 'night'.");
            return;
        }

        CustomerProfile::where('status', 'Active')
            ->whereHas('package') // Ensure they have a package
            ->chunk(100, function ($profiles) use ($mode) {
                foreach ($profiles as $profile) {
                    $this->switchProfile($profile, $mode);
                }
            });

        $this->info("Bandwidth sync completed for {$mode} mode.");
    }

    private function switchProfile(CustomerProfile $profile, $mode)
    {
        try {
            // Assume package has mikrotik_profile_name for Day, and maybe we append '-night' for Night.
            // Or you can have a specific 'night_profile_name' in the DB.
            $profileName = $profile->package->mikrotik_profile_name;
            
            if ($mode === 'night') {
                $profileName .= '-Night'; // Expected pattern: "10Mbps-Night"
            }

            $mikrotik = new MikrotikService($profile->router);
            
            // Apply Profile
            $mikrotik->changeProfile($profile->pppoe_username, $profileName);
            
            // Disconnect to force reconnect with new speed
            $mikrotik->disconnectActiveSession($profile->pppoe_username);
            
            $this->line("Switched {$profile->pppoe_username} to {$profileName}");

        } catch (\Exception $e) {
            Log::error("Bandwidth Sync Failed for {$profile->pppoe_username}: " . $e->getMessage());
        }
    }
}
