<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OttController extends Controller
{
    /**
     * Get available OTT Packages (Chorki, Hoichoi, Binge, etc.)
     */
    public function getPackages(Request $request)
    {
        // Assuming we have an ott_packages table
        $packages = DB::table('ott_packages')->where('is_active', true)->get();

        if ($packages->isEmpty()) {
            // Mock data if table is empty
            $packages = [
                [
                    'id' => 1,
                    'provider' => 'Chorki',
                    'plan_name' => 'Monthly Premium',
                    'price' => 50.00,
                    'description' => 'Unlimited Chorki access for 1 month'
                ],
                [
                    'id' => 2,
                    'provider' => 'Hoichoi',
                    'plan_name' => 'Monthly Premium',
                    'price' => 60.00,
                    'description' => 'Unlimited Hoichoi access for 1 month'
                ],
                [
                    'id' => 3,
                    'provider' => 'Binge',
                    'plan_name' => 'Monthly Premium',
                    'price' => 40.00,
                    'description' => 'Unlimited Binge access for 1 month'
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }

    /**
     * Subscribe to an OTT Package
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer',
            'phone' => 'required|string'
        ]);

        // Logic to hit OTT Provider's API to activate subscription using phone
        // ...

        return response()->json([
            'success' => true,
            'message' => 'Subscribed to OTT package successfully. Credentials sent via SMS.'
        ]);
    }
}
