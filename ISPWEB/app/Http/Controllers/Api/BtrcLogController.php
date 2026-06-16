<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BtrcLogController extends Controller
{
    /**
     * Receive Syslog/API NAT session data from MikroTik
     * MikroTik should be configured to hit this webhook on disconnect or periodically
     */
    public function receiveLog(Request $request)
    {
        // Simple Auth for MikroTik
        $secret = env('BTRC_LOG_SECRET', 'mikrotik_btrc_secret');
        if ($request->header('X-Log-Secret') !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'username' => 'required|string',
            'src_ip' => 'required|ip',
            'dst_ip' => 'nullable|ip',
            'mac_address' => 'nullable|string',
            'session_start' => 'nullable|date',
            'session_end' => 'nullable|date',
            'bytes_up' => 'nullable|numeric',
            'bytes_down' => 'nullable|numeric'
        ]);

        try {
            \App\Jobs\ProcessBtrcLog::dispatch($request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("BTRC Log Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to queue log'], 500);
        }
    }
}
