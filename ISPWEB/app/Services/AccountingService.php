<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingService
{
    /**
     * Record a Double-Entry Journal Transaction
     * Ensures Debit == Credit
     */
    public function recordTransaction($description, $entries, $reference = null, $date = null)
    {
        if (empty($date)) {
            $date = now()->toDateString();
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($entries as $entry) {
            $totalDebit += $entry['debit'] ?? 0;
            $totalCredit += $entry['credit'] ?? 0;
        }

        // The fundamental rule of double-entry accounting
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \Exception("Accounting Error: Debits (" . $totalDebit . ") must equal Credits (" . $totalCredit . ")");
        }

        DB::beginTransaction();

        try {
            // Create Journal Header
            $journalId = DB::table('accounting_journal')->insertGetId([
                'transaction_id' => 'TRX-' . strtoupper(Str::random(10)),
                'transaction_date' => $date,
                'reference' => $reference,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create Ledger Entries
            foreach ($entries as $entry) {
                // Find account ID by Code
                $account = DB::table('chart_of_accounts')->where('account_code', $entry['account_code'])->first();
                if (!$account) {
                    throw new \Exception("Invalid Account Code: " . $entry['account_code']);
                }

                DB::table('accounting_ledger')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $account->id,
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
