<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the main Dashboard with aggregated data
     */
    public function index()
    {
        // Active Users
        $activeUsersCount = CustomerProfile::where('status', 'Active')->count();
        
        // Expired Users
        $expiredUsersCount = CustomerProfile::where('status', 'Expired')->count();
        
        // Total Revenue for current month
        $monthlyRevenue = Invoice::where('payment_status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');
            
        // Open Tickets
        $openTicketsCount = DB::table('tickets')->whereIn('status', ['open', 'in_progress'])->count();

        // Revenue Chart Data (Last 8 months)
        $chartData = [];
        $chartLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $sum = Invoice::where('payment_status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
            
            $chartData[] = $sum ?: 0;
            $chartLabels[] = $date->format('M');
        }

        // Recent Customers
        $recentCustomers = CustomerProfile::with('user', 'package')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'activeUsersCount',
            'expiredUsersCount',
            'monthlyRevenue',
            'openTicketsCount',
            'chartData',
            'chartLabels',
            'recentCustomers'
        ));
    }
}
