@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure global application preferences.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border p-6 glass-card">
        <form class="space-y-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">General Configuration</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                    <input type="text" value="ISP Cloud Saas" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border focus:ring-brand-500 focus:border-brand-500 dark:text-white">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                        <select class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border focus:ring-brand-500 focus:border-brand-500 dark:text-white">
                            <option>BDT (৳)</option>
                            <option>USD ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                        <select class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border focus:ring-brand-500 focus:border-brand-500 dark:text-white">
                            <option>Asia/Dhaka</option>
                            <option>UTC</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-gray-100 dark:border-dark-border">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Maintenance Mode</h4>
                        <p class="text-xs text-gray-500">Temporarily disable customer portal access.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                    </label>
                </div>
            </div>
            
            <div class="pt-6 border-t border-gray-100 dark:border-dark-border">
                <button type="button" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
