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
}
