@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your administrator profile and preferences.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border p-6 glass-card">
        <div class="flex items-center gap-6 mb-8">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=6366F1&color=fff" class="w-20 h-20 rounded-2xl shadow-lg glow-primary">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'Administrator' }}</h2>
                <p class="text-gray-500">{{ auth()->user()->email ?? 'admin@isp.local' }}</p>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-200 dark:border-brand-500/20">
                    Admin Role
                </div>
            </div>
        </div>

        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" value="{{ auth()->user()->name ?? 'Administrator' }}" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border focus:ring-brand-500 focus:border-brand-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" value="{{ auth()->user()->email ?? 'admin@isp.local' }}" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border focus:ring-brand-500 focus:border-brand-500 dark:text-white">
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100 dark:border-dark-border">
                <button type="button" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-md">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
