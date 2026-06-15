<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CustomerProfile;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Web View for BI Dashboard
     */
    public function index()
    {
        $churnData = $this->calculatePredictiveChurn();
        return view('analytics', compact('churnData'));
    }

    /**
     * Calculate Predictive Churn Risk for all active users
     */
    private function calculatePredictiveChurn()
    {
        $profiles = CustomerProfile::with('user')->where('status', 'Active')->get();
        $riskList = [];

        foreach ($profiles as $profile) {
            $score = 0;
            $riskFactors = [];

            // 1. Check Ticket Frequency (More than 2 tickets in last 30 days = High Risk)
            $ticketCount = DB::table('tickets')
                ->where('customer_id', $profile->user_id)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            if ($ticketCount >= 3) {
                $score += 50;
                $riskFactors[] = "Frequent Downtime ($ticketCount tickets)";
            } elseif ($ticketCount == 2) {
                $score += 30;
                $riskFactors[] = "Recent Connectivity Issues";
            }

            // 2. Check Payment Behavior (Grace period usage)
            if (!empty($profile->grace_period_used_at)) {
                $graceUsedDate = Carbon::parse($profile->grace_period_used_at);
                if ($graceUsedDate->diffInDays(Carbon::now()) <= 30) {
                    $score += 20;
                    $riskFactors[] = "Used Emergency Balance";
                }
            }

            // 3. Simulated Router Ping Drops (Mocking metrics from Mikrotik resource logs)
            // In a real scenario, query the btrc_logs or a router_metrics table
            $mockedPingDrops = rand(0, 15); 
            if ($mockedPingDrops > 10) {
                $score += 20;
                $riskFactors[] = "High Packet Loss Detected";
            }

            // Determine Risk Level
            $riskLevel = 'Low';
            $color = 'emerald';
            
            if ($score >= 70) {
                $riskLevel = 'High';
                $color = 'red';
            } elseif ($score >= 30) {
                $riskLevel = 'Medium';
                $color = 'amber';
            }

            // Only push Medium to High risk customers to the retention team dashboard
            if ($score >= 30) {
                $riskList[] = [
                    'customer_name' => $profile->user->name,
                    'phone' => $profile->user->email, // Mocking phone with email for now
                    'pppoe' => $profile->pppoe_username,
                    'score' => $score,
                    'level' => $riskLevel,
                    'color' => $color,
                    'factors' => implode(', ', $riskFactors)
                ];
            }
        }

        // Sort by highest risk first
        usort($riskList, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $riskList;
    }
}
