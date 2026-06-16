<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerWebController extends Controller
{
    public function index()
    {
        // Fetch real customers if the table exists, otherwise return empty array
        $customers = [];
        if (DB::getSchemaBuilder()->hasTable('users')) {
            $customers = DB::table('users')->where('role', 'customer')->get();
        }

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        \App\Models\User::factory()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer'
        ]);

        return redirect('/customers')->with('success', 'Customer added successfully!');
    }
}
