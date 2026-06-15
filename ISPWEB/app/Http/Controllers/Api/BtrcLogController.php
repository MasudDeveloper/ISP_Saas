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
            DB::table('btrc_logs')->insert([
                'username' => $request->username,
                'src_ip' => $request->src_ip,
                'dst_ip' => $request->dst_ip,
                'mac_address' => $request->mac_address,
                'session_start' => $request->session_start ? date('Y-m-d H:i:s', strtotime($request->session_start)) : now(),
                'session_end' => $request->session_end ? date('Y-m-d H:i:s', strtotime($request->session_end)) : now(),
                'bytes_up' => $request->bytes_up ?? 0,
                'bytes_down' => $request->bytes_down ?? 0
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("BTRC Log Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to save log'], 500);
        }
    }
}
