<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Assuming models are generated (Ticket, Technician)
// use App\Models\Ticket;
// use App\Models\Technician;

class TicketController extends Controller
{
    /**
     * Submit a support ticket from the Android App
     */
    public function submitTicket(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        $customerId = $request->user()->id;

        // Create Ticket (Raw DB insert for brevity)
        $ticketId = DB::table('tickets')->insertGetId([
            'customer_id' => $customerId,
            'category' => $request->category,
            'description' => $request->description,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Find nearest free technician within 2 KM using Haversine formula
        $nearestTechnician = $this->findNearestFreeTechnician($request->lat, $request->lng, 2.0);

        if ($nearestTechnician) {
            DB::table('tickets')->where('id', $ticketId)->update([
                'assigned_technician_id' => $nearestTechnician->id,
                'status' => 'in_progress'
            ]);
            
            DB::table('technicians')->where('id', $nearestTechnician->id)->update([
                'is_free' => false
            ]);

            // Send Firebase Push Notification
            if ($nearestTechnician->fcm_token) {
                $this->sendFirebaseNotification($nearestTechnician->fcm_token, $ticketId, $request->category);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket submitted! A technician has been assigned and is on the way.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket submitted! We will assign a technician shortly.'
        ]);
    }

    /**
     * Haversine Formula Implementation
     */
    private function findNearestFreeTechnician($lat, $lng, $maxDistanceKm)
    {
        /*
         * 6371 = Earth radius in Kilometers
         * Returns distance in KM
         */
        $technician = DB::table('technicians')
            ->select('id', 'fcm_token', DB::raw(
                "( 6371 * acos( cos( radians($lat) ) *
                cos( radians( current_lat ) ) *
                cos( radians( current_lng ) - radians($lng) ) +
                sin( radians($lat) ) *
                sin( radians( current_lat ) ) ) ) AS distance"
            ))
            ->where('is_free', true)
            ->whereNotNull('current_lat')
            ->having('distance', '<=', $maxDistanceKm)
            ->orderBy('distance')
            ->first();

        return $technician;
    }

    /**
     * Send Push Notification via Firebase (FCM API v1 or Legacy)
     */
    private function sendFirebaseNotification($fcmToken, $ticketId, $category)
    {
        $serverKey = env('FCM_SERVER_KEY', 'your-firebase-server-key'); // Keep this secret
        
        $data = [
            "to" => $fcmToken,
            "notification" => [
                "title" => "New Ticket Assigned!",
                "body" => "Category: {$category}. Please check your map for routing.",
                "sound" => "default"
            ],
            "data" => [
                "ticket_id" => $ticketId,
                "action" => "OPEN_MAP"
            ]
        ];

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            
            $response = curl_exec($ch);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("FCM Error: " . $e->getMessage());
        }
    }

    /**
     * Technician updates their live location (For Ride-sharing like Live Tracking)
     */
    public function updateTechnicianLocation(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'isp_staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        // Update tech's current location in DB
        DB::table('technicians')
            ->where('user_id', $user->id)
            ->update([
                'current_lat' => $request->latitude,
                'current_lng' => $request->longitude,
                'last_ping' => now()
            ]);

        // Optional: In a production app, push this directly to Firebase Realtime Database 
        // so the customer app can listen to it without polling Laravel.

        return response()->json(['success' => true]);
    }

    /**
     * Get all active technician locations for Admin Map
     */
    public function getTechnicianLocations(Request $request)
    {
        $technicians = DB::table('technicians')
            ->select('id', 'name', 'current_lat', 'current_lng', 'last_ping', 'is_free')
            ->whereNotNull('current_lat')
            ->get();
            
        return response()->json(['technicians' => $technicians]);
    }
}
