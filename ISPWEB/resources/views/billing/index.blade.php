@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing & Payments</h1>
            <p class="text-sm text-gray-500 mt-1">Manage invoices, subscriptions, and financial logs.</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border overflow-hidden glass-card">
        <div class="p-6 border-b border-gray-200 dark:border-dark-border">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Invoices</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-dark-bg/50 border-b border-gray-200 dark:border-dark-border text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Invoice ID</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Gateway</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border text-sm">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-border/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-300">
                            #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-medium">
                            ৳{{ number_format($invoice->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ ucfirst($invoice->gateway ?? 'Manual') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($invoice->status == 'paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Paid</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="receipt" class="w-12 h-12 mx-auto text-gray-400 mb-3 opacity-50"></i>
                            <p>No billing invoices generated yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
