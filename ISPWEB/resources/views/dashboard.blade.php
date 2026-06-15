@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Overview</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back, here's what's happening today.</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 text-sm font-medium bg-white dark:bg-dark-surface border border-gray-200 dark:border-dark-border rounded-xl hover:bg-gray-50 dark:hover:bg-dark-border transition-colors">
                <i data-lucide="download" class="w-4 h-4 inline-block mr-2"></i> Export
            </button>
            <button class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-lg glow-primary">
                <i data-lucide="plus" class="w-4 h-4 inline-block mr-2"></i> Add Customer
            </button>
        </div>
    </div>

    <!-- Glowing Animated Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Users -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl p-6 border border-gray-200 dark:border-dark-border relative overflow-hidden group hover:border-brand-500/50 transition-colors">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-500/10 rounded-full blur-2xl group-hover:bg-brand-500/20 transition-all duration-500"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Users</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">2,420</h3>
                </div>
                <div class="p-3 bg-brand-50 dark:bg-brand-500/10 rounded-xl text-brand-500 glow-primary">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="flex items-center text-sm relative z-10">
                <span class="text-emerald-500 flex items-center font-medium"><i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 12%</span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Expired Users -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl p-6 border border-gray-200 dark:border-dark-border relative overflow-hidden group hover:border-red-500/50 transition-colors">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-500/10 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all duration-500"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-500">Expired Users</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">145</h3>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl text-red-500 glow-danger">
                    <i data-lucide="user-minus" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="flex items-center text-sm relative z-10">
                <span class="text-red-500 flex items-center font-medium"><i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 4%</span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl p-6 border border-gray-200 dark:border-dark-border relative overflow-hidden group hover:border-emerald-500/50 transition-colors">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">$45.2k</h3>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500 glow-success">
                    <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="flex items-center text-sm relative z-10">
                <span class="text-emerald-500 flex items-center font-medium"><i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 8.2%</span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl p-6 border border-gray-200 dark:border-dark-border relative overflow-hidden group hover:border-amber-500/50 transition-colors">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all duration-500"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-500">Open Tickets</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">28</h3>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500 glow-warning">
                    <i data-lucide="ticket" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="flex items-center text-sm relative z-10">
                <span class="text-emerald-500 flex items-center font-medium"><i data-lucide="trending-down" class="w-4 h-4 mr-1"></i> 12%</span>
                <span class="text-gray-500 ml-2">resolved today</span>
            </div>
        </div>
    </div>

    <!-- Chart & Top Packages -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border lg:col-span-2 p-6 glass-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Overview</h2>
                <select class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-sm rounded-lg px-3 py-1.5 focus:ring-brand-500 focus:border-brand-500 dark:text-gray-300">
                    <option>This Year</option>
                    <option>Last Year</option>
                </select>
            </div>
            <div id="revenueChart" class="h-[300px] w-full"></div>
        </div>

        <!-- System Health / Quick Stats -->
        <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border p-6 glass-card">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Routers Status</h2>
            <div class="space-y-6">
                <!-- Router Item -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full glow-success"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Core Router 1</p>
                            <p class="text-xs text-gray-500">Uptime: 45d 12h</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-lg border border-emerald-200 dark:border-emerald-500/20">Online</span>
                </div>
                <!-- Router Item -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full glow-success"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Zone B Router</p>
                            <p class="text-xs text-gray-500">Uptime: 12d 4h</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-lg border border-emerald-200 dark:border-emerald-500/20">Online</span>
                </div>
                <!-- Router Item -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full glow-danger animate-pulse"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Reseller Node 3</p>
                            <p class="text-xs text-gray-500">Timeout 2m ago</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-500/20">Offline</span>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                <button class="w-full py-2.5 text-sm font-medium text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-500/10 rounded-xl hover:bg-brand-100 dark:hover:bg-brand-500/20 transition-colors">
                    View Full Network Map
                </button>
            </div>
        </div>
    </div>

    <!-- Advanced Customers Table -->
    <div class="bg-white dark:bg-dark-surface rounded-2xl border border-gray-200 dark:border-dark-border overflow-hidden glass-card">
        <div class="p-6 border-b border-gray-200 dark:border-dark-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Customers</h2>
            
            <div class="flex gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                    <input type="text" placeholder="Search by name, IP, PPPoE..." class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-gray-200 outline-none transition-all">
                </div>
                <button class="p-2 text-gray-500 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl hover:bg-gray-100 dark:hover:bg-dark-border transition-colors">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-dark-bg/50 border-b border-gray-200 dark:border-dark-border text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">PPPoE Details</th>
                        <th class="px-6 py-4 font-medium">Package</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Expiry Date</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border text-sm">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-border/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Arif+Hossain&background=6366F1&color=fff" class="w-8 h-8 rounded-full">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">Arif Hossain</div>
                                    <div class="text-gray-500 text-xs">arif@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-900 dark:text-gray-300 font-medium">arif_pppoe</div>
                            <div class="text-gray-500 text-xs mt-0.5">IP: 103.111.x.x</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-dark-border text-gray-700 dark:text-gray-300 rounded-md text-xs font-medium">20 Mbps Pro</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            Nov 24, 2026
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="p-1.5 text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10 rounded-lg transition-colors tooltip" title="Quick Pay">
                                    <i data-lucide="banknote" class="w-4 h-4"></i>
                                </button>
                                <button class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-lg transition-colors tooltip" title="Disable Line">
                                    <i data-lucide="power-off" class="w-4 h-4"></i>
                                </button>
                                <button class="p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-dark-border rounded-lg transition-colors tooltip" title="View Profile">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-border/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Kamrul+Islam&background=F59E0B&color=fff" class="w-8 h-8 rounded-full">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">Kamrul Islam</div>
                                    <div class="text-gray-500 text-xs">kamrul@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-900 dark:text-gray-300 font-medium">kamrul_isp</div>
                            <div class="text-gray-500 text-xs mt-0.5">IP: Dynamic</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-dark-border text-gray-700 dark:text-gray-300 rounded-md text-xs font-medium">10 Mbps Basic</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Expired
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-red-500 font-medium">
                            Nov 10, 2026
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="p-1.5 text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10 rounded-lg transition-colors tooltip" title="Quick Pay">
                                    <i data-lucide="banknote" class="w-4 h-4"></i>
                                </button>
                                <button class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-lg transition-colors tooltip" title="Disable Line">
                                    <i data-lucide="power-off" class="w-4 h-4"></i>
                                </button>
                                <button class="p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-dark-border rounded-lg transition-colors tooltip" title="View Profile">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination Mockup -->
        <div class="p-4 border-t border-gray-200 dark:border-dark-border flex items-center justify-between">
            <p class="text-sm text-gray-500">Showing <span class="font-medium text-gray-900 dark:text-white">1</span> to <span class="font-medium text-gray-900 dark:text-white">10</span> of <span class="font-medium text-gray-900 dark:text-white">97</span> results</p>
            <div class="flex gap-1">
                <button class="px-3 py-1 border border-gray-200 dark:border-dark-border rounded-lg text-sm text-gray-500 hover:bg-gray-50 dark:hover:bg-dark-border disabled:opacity-50" disabled>Prev</button>
                <button class="px-3 py-1 bg-brand-500 text-white rounded-lg text-sm font-medium">1</button>
                <button class="px-3 py-1 border border-gray-200 dark:border-dark-border rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-border">2</button>
                <button class="px-3 py-1 border border-gray-200 dark:border-dark-border rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-border">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection

@stack('scripts')
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const isDark = document.documentElement.classList.contains('dark');
        
        const options = {
            series: [{
                name: 'Revenue',
                data: [31000, 40000, 28000, 51000, 42000, 109000, 100000, 120000]
            }],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                background: 'transparent',
                zoom: { enabled: false }
            },
            colors: ['#6366F1'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: isDark ? '#9CA3AF' : '#6B7280' }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) { return "$" + (value/1000) + "k"; },
                    style: { colors: isDark ? '#9CA3AF' : '#6B7280' }
                }
            },
            grid: {
                borderColor: isDark ? '#1F2937' : '#E5E7EB',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } }
            },
            theme: { mode: isDark ? 'dark' : 'light' }
        };

        const chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();

        // Listen for dark mode changes in Alpine
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if(mutation.attributeName === 'class') {
                    const isDarkTheme = document.documentElement.classList.contains('dark');
                    chart.updateOptions({
                        theme: { mode: isDarkTheme ? 'dark' : 'light' },
                        grid: { borderColor: isDarkTheme ? '#1F2937' : '#E5E7EB' },
                        xaxis: { labels: { style: { colors: isDarkTheme ? '#9CA3AF' : '#6B7280' } } },
                        yaxis: { labels: { style: { colors: isDarkTheme ? '#9CA3AF' : '#6B7280' } } }
                    });
                }
            });
        });
        
        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endpush
