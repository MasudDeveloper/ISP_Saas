<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GisMapController extends Controller
{
    /**
     * Show the GIS Map Dashboard
     */
    public function index()
    {
        // Fetch existing data for the map
        $tjBoxes = DB::table('tj_boxes')->get();
        $fiberLines = DB::table('fiber_lines')->get();
        
        return view('gis_map', compact('tjBoxes', 'fiberLines'));
    }

    /**
     * Save a new Splitter / TJ Box
     */
    public function saveTjBox(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:tj_boxes,name',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'total_ports' => 'required|integer'
        ]);

        $id = DB::table('tj_boxes')->insertGetId([
            'name' => $request->name,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'total_ports' => $request->total_ports,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'id' => $id, 'message' => 'TJ Box Saved']);
    }

    /**
     * Save a new Fiber Line routing (Polyline)
     */
    public function saveFiberLine(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'coordinates' => 'required|json', // Array of latLngs
            'color' => 'required|string'
        ]);

        DB::table('fiber_lines')->insert([
            'name' => $request->name,
            'coordinates' => $request->coordinates,
            'color' => $request->color,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Fiber Route Saved']);
    }
}
