<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingWebController extends Controller
{
    public function index()
    {
        // Fetch billing invoices
        $invoices = [];
        if (DB::getSchemaBuilder()->hasTable('invoices')) {
            $invoices = DB::table('invoices')->orderBy('created_at', 'desc')->get();
        }

        return view('billing.index', compact('invoices'));
    }
}
