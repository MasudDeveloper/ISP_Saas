<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DisconnectExpiredCustomers;
use App\Console\Commands\SyncBandwidthProfiles;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule the daily auto-disconnect job
Schedule::command(DisconnectExpiredCustomers::class)->dailyAt('00:00');

// Bandwidth Day/Night Scheduler
Schedule::command(SyncBandwidthProfiles::class, ['night'])->dailyAt('00:01'); // Switch to Night Speed at 12:01 AM
Schedule::command(SyncBandwidthProfiles::class, ['day'])->dailyAt('08:00');   // Switch back to Day Speed at 8:00 AM
