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
            $invoices = \App\Models\Invoice::with('user')->orderBy('created_at', 'desc')->get();
            
            // Add Demo Data if empty
            if ($invoices->isEmpty()) {
                $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
                
                $demoInvoices = [];
                for($i = 1; $i <= 5; $i++) {
                    $demoInvoices[] = [
                        'invoice_number' => 'INV-' . rand(1000, 9999) . '-' . $i,
                        'customer_id' => $user->id,
                        'payment_status' => 'paid',
                        'amount' => 1200.00,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()
                    ];
                }
                \App\Models\Invoice::insert($demoInvoices);

                $invoices = \App\Models\Invoice::with('user')->orderBy('created_at', 'desc')->get();
            }
        }

        return view('billing.index', compact('invoices'));
    }
}
