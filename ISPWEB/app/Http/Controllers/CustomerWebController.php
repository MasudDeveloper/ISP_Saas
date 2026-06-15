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
}
