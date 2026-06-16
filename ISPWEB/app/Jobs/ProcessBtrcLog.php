<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBtrcLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::table('btrc_logs')->insert([
                'username' => $this->logData['username'],
                'src_ip' => $this->logData['src_ip'],
                'dst_ip' => $this->logData['dst_ip'],
                'mac_address' => $this->logData['mac_address'],
                'session_start' => $this->logData['session_start'] ? date('Y-m-d H:i:s', strtotime($this->logData['session_start'])) : now(),
                'session_end' => $this->logData['session_end'] ? date('Y-m-d H:i:s', strtotime($this->logData['session_end'])) : now(),
                'bytes_up' => $this->logData['bytes_up'] ?? 0,
                'bytes_down' => $this->logData['bytes_down'] ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to process BTRC log queue: " . $e->getMessage());
        }
    }
}
