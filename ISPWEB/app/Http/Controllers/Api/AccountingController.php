<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Pay an Expense (e.g., Staff Salary, Pole Rent)
     * Decreases Cash (Credit 1000) and Increases Expense (Debit 5xxx)
     */
    public function recordExpense(Request $request)
    {
        $request->validate([
            'expense_account_code' => 'required|exists:chart_of_accounts,account_code',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string'
        ]);

        $entries = [
            [
                'account_code' => $request->expense_account_code, // e.g. 5100 (Salary)
                'debit' => $request->amount,
                'credit' => 0
            ],
            [
                'account_code' => '1000', // Cash Account
                'debit' => 0,
                'credit' => $request->amount
            ]
        ];

        try {
            $this->accountingService->recordTransaction($request->description, $entries);
            return response()->json(['success' => true, 'message' => 'Expense recorded successfully via double-entry']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Generate Profit and Loss (Income Statement)
     */
    public function generatePnL(Request $request)
    {
        // PnL = Total Revenue - Total Expenses
        
        $revenueAccounts = DB::table('chart_of_accounts')->where('account_type', 'Revenue')->pluck('id');
        $expenseAccounts = DB::table('chart_of_accounts')->where('account_type', 'Expense')->pluck('id');

        $totalRevenue = DB::table('accounting_ledger')
            ->whereIn('account_id', $revenueAccounts)
            ->sum(DB::raw('credit - debit')); // Revenue has credit balance

        $totalExpense = DB::table('accounting_ledger')
            ->whereIn('account_id', $expenseAccounts)
            ->sum(DB::raw('debit - credit')); // Expense has debit balance

        $netProfit = $totalRevenue - $totalExpense;

        return response()->json([
            'report' => 'Profit and Loss Statement',
            'period' => 'All Time', // Can be filtered by date in production
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpense,
            'net_profit' => $netProfit,
            'status' => $netProfit >= 0 ? 'Profitable' : 'Loss'
        ]);
    }
}
