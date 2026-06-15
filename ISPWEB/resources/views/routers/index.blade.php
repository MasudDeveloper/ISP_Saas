@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Routers</h1>
            <p class="text-sm text-gray-500 mt-1">Manage Mikrotik nodes and network hardware.</p>
        </div>
        <button class="px-4 py-2 text-sm font-medium bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-colors shadow-lg glow-success">
            <i data-lucide="plus" class="w-4 h-4 inline-block mr-2"></i> Add Router
        </button>
    </div>

    <!-- Routers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($routers as $router)
        <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border p-6 glass-card relative overflow-hidden group">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-brand-50 dark:bg-brand-500/10 rounded-xl text-brand-500">
                        <i data-lucide="server" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $router->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $router->ip_address }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                </span>
            </div>
            
            <div class="space-y-3 mt-6">
                <div>
                    <div class="flex justify-between text-xs mb-1 text-gray-500">
                        <span>CPU Usage</span>
                        <span>42%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-dark-bg rounded-full h-1.5">
                        <div class="bg-brand-500 h-1.5 rounded-full" style="width: 42%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1 text-gray-500">
                        <span>RAM Usage</span>
                        <span>68%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-dark-bg rounded-full h-1.5">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: 68%"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-dark-border flex justify-between items-center text-sm">
                <span class="text-gray-500">API Port: {{ $router->api_port }}</span>
                <button class="text-brand-600 font-medium hover:text-brand-700">Configure</button>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-dark-surface rounded-2xl border border-dashed border-gray-300 dark:border-dark-border p-12 text-center">
            <i data-lucide="router" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No Routers Connected</h3>
            <p class="text-gray-500 mt-1">Connect your first Mikrotik router to start managing PPPoE sessions.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
