@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Predictive Churn Engine</h1>
            <p class="text-sm text-gray-500 mt-1">AI-driven analysis of customer behavior to prevent disconnections.</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-lg glow-primary">
                <i data-lucide="refresh-cw" class="w-4 h-4 inline-block mr-2"></i> Recalculate Risk
            </button>
        </div>
    </div>

    <!-- Alert Box -->
    <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 flex items-start gap-4">
        <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-lg text-red-600 dark:text-red-400">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-red-800 dark:text-red-400">Attention Required</h3>
            <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                Found {{ count($churnData) }} customers at risk of leaving the service within the next 30 days due to network instability or payment issues.
            </p>
        </div>
    </div>

    <!-- Churn Risk Table -->
    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border overflow-hidden glass-card mt-6">
        <div class="p-6 border-b border-gray-200 dark:border-dark-border">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">High Risk Customers (Retention Queue)</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-dark-bg/50 border-b border-gray-200 dark:border-dark-border text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Customer Details</th>
                        <th class="px-6 py-4 font-medium">Risk Score</th>
                        <th class="px-6 py-4 font-medium">Identified Factors</th>
                        <th class="px-6 py-4 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border text-sm">
                    @forelse($churnData as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-border/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-{{ $customer['color'] }}-100 dark:bg-{{ $customer['color'] }}-500/20 flex items-center justify-center text-{{ $customer['color'] }}-600 dark:text-{{ $customer['color'] }}-400 font-bold text-xs">
                                    {{ substr($customer['customer_name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $customer['customer_name'] }}</div>
                                    <div class="text-gray-500 text-xs">{{ $customer['pppoe'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-gray-200 dark:bg-dark-border rounded-full overflow-hidden">
                                    <div class="h-full bg-{{ $customer['color'] }}-500" style="width: {{ $customer['score'] }}%"></div>
                                </div>
                                <span class="text-{{ $customer['color'] }}-600 dark:text-{{ $customer['color'] }}-400 font-bold">{{ $customer['score'] }}%</span>
                                <span class="text-xs text-gray-500">({{ $customer['level'] }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $customer['factors'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button class="px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 rounded-lg hover:bg-brand-100 transition-colors flex items-center justify-end gap-1 ml-auto">
                                <i data-lucide="phone-call" class="w-3 h-3"></i> Call Now
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No customers are currently at risk. Great job!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
