<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Add new hardware to inventory
     */
    public function stockInHardware(Request $request)
    {
        $request->validate([
            'item_type' => 'required|string',
            'brand' => 'required|string',
            'serial_number' => 'required|string|unique:inventory_items',
            'mac_address' => 'nullable|string|unique:inventory_items'
        ]);

        DB::table('inventory_items')->insert([
            'item_type' => $request->item_type,
            'brand' => $request->brand,
            'serial_number' => $request->serial_number,
            'mac_address' => $request->mac_address,
            'status' => 'in_stock',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Hardware added to stock']);
    }

    /**
     * Assign hardware to a customer
     */
    public function assignHardware(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|exists:inventory_items,serial_number',
            'customer_id' => 'required|exists:users,id'
        ]);

        DB::table('inventory_items')
            ->where('serial_number', $request->serial_number)
            ->update([
                'status' => 'deployed',
                'assigned_customer_id' => $request->customer_id,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Hardware assigned to customer']);
    }

    /**
     * Register a new Drum of Cable
     */
    public function addCableDrum(Request $request)
    {
        $request->validate([
            'drum_number' => 'required|string|unique:inventory_drums',
            'cable_type' => 'required|string',
            'total_meters' => 'required|numeric'
        ]);

        DB::table('inventory_drums')->insert([
            'drum_number' => $request->drum_number,
            'cable_type' => $request->cable_type,
            'total_meters' => $request->total_meters,
            'used_meters' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Cable drum added to inventory']);
    }

    /**
     * Record Cable Usage for a Customer
     */
    public function recordCableUsage(Request $request)
    {
        $request->validate([
            'drum_id' => 'required|exists:inventory_drums,id',
            'customer_id' => 'required|exists:users,id',
            'meters_used' => 'required|numeric|min:1'
        ]);

        $drum = DB::table('inventory_drums')->where('id', $request->drum_id)->first();
        $remaining = $drum->total_meters - $drum->used_meters;

        if ($request->meters_used > $remaining) {
            return response()->json(['error' => 'Not enough cable left on this drum. Remaining: ' . $remaining . 'm'], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('inventory_cable_usage')->insert([
                'drum_id' => $request->drum_id,
                'customer_id' => $request->customer_id,
                'meters_used' => $request->meters_used,
                'used_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('inventory_drums')
                ->where('id', $request->drum_id)
                ->increment('used_meters', $request->meters_used);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cable usage recorded successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to record usage'], 500);
        }
    }
}
