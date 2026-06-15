@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Customers</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your ISP subscribers and their PPPoE connections.</p>
        </div>
        <button class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-lg glow-primary">
            <i data-lucide="plus" class="w-4 h-4 inline-block mr-2"></i> Add Customer
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border overflow-hidden glass-card">
        <div class="p-6 border-b border-gray-200 dark:border-dark-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Subscribers</h2>
            
            <div class="flex gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                    <input type="text" placeholder="Search by name, PPPoE..." class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-gray-200 outline-none transition-all">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-dark-bg/50 border-b border-gray-200 dark:border-dark-border text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Email</th>
                        <th class="px-6 py-4 font-medium">Joined Date</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border text-sm">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-border/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=6366F1&color=fff" class="w-8 h-8 rounded-full">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $customer->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button class="text-brand-600 hover:text-brand-700 font-medium">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="users" class="w-12 h-12 mx-auto text-gray-400 mb-3 opacity-50"></i>
                            <p>No customers found in database.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
