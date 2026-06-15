<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Log;

class RouterControlController extends Controller
{
    /**
     * Get Current WiFi Settings (Mocking TR-069 / OMCI / Mikrotik Wireless API)
     */
    public function getWifiSettings(Request $request)
    {
        $user = $request->user();
        
        // In a real scenario, this would query an ACS Server (GenieACS) via TR-069
        // or query the customer's CPE directly if accessible.
        
        // Simulating the response
        return response()->json([
            'ssid' => $user->name . '_WiFi',
            'status' => 'Active',
            'connected_devices' => rand(2, 8)
        ]);
    }

    /**
     * Update WiFi Name (SSID) and Password
     */
    public function updateWifiSettings(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'ssid' => 'required|string|min:3|max:32',
            'password' => 'required|string|min:8'
        ]);

        $profile = CustomerProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // --- TR-069 / ACS SERVER INTEGRATION LOGIC GOES HERE ---
        // Example:
        // $acsClient = new \GuzzleHttp\Client();
        // $acsClient->post('http://acs-server:7557/tasks', [
        //     'json' => [
        //         'name' => 'setParameterValues',
        //         'device' => $profile->cpe_mac_address, // Assuming we store CPE MAC
        //         'parameterValues' => [
        //             ['InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID', $request->ssid],
        //             ['InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.PreSharedKey', $request->password]
        //         ]
        //     ]
        // ]);
        
        Log::info("WiFi Update Requested by User {$user->id} -> New SSID: {$request->ssid}");

        return response()->json([
            'success' => true, 
            'message' => 'WiFi settings update command sent to your router. It may take up to 60 seconds to apply.'
        ]);
    }

    /**
     * Block a connected device (MAC Address)
     */
    public function blockDevice(Request $request)
    {
        $request->validate([
            'mac_address' => 'required|string'
        ]);

        // Logic to push MAC filter to CPE via TR-069
        Log::info("Device Block Requested: {$request->mac_address}");

        return response()->json(['success' => true, 'message' => 'Device blocked successfully']);
    }
}
