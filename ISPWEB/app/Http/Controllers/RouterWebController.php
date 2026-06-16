<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouterWebController extends Controller
{
    public function index()
    {
        // Fetch routers
        $routers = [];
        if (DB::getSchemaBuilder()->hasTable('mikrotik_routers')) {
            $routers = DB::table('mikrotik_routers')->get();
        }

        return view('routers.index', compact('routers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'api_port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        \App\Models\Router::create([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'api_port' => $request->api_port,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Crypt::encryptString($request->password)
        ]);

        return redirect('/routers')->with('success', 'Router added successfully!');
    }
}
