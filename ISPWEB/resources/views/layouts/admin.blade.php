<!DOCTYPE html>
<html lang="en" class="dark" x-data="{ darkMode: true, sidebarOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP SaaS Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0B0F19', // Deep modern dark
                            surface: '#111827',
                            border: '#1F2937'
                        },
                        brand: {
                            500: '#6366F1', // Indigo modern
                            600: '#4F46E5'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Glassmorphism Classes */
        .glass-card {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .light-glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Glowing Effects */
        .glow-primary { box-shadow: 0 0 20px -5px rgba(99, 102, 241, 0.4); }
        .glow-success { box-shadow: 0 0 20px -5px rgba(34, 197, 94, 0.4); }
        .glow-danger { box-shadow: 0 0 20px -5px rgba(239, 68, 68, 0.4); }
        .glow-warning { box-shadow: 0 0 20px -5px rgba(245, 158, 11, 0.4); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4B5563; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-dark-bg text-gray-800 dark:text-gray-100 transition-colors duration-300">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 transition-transform duration-300 ease-in-out transform bg-white dark:bg-dark-surface border-r border-gray-200 dark:border-dark-border lg:translate-x-0 lg:static lg:inset-0 glass-card">
            <div class="flex items-center justify-center h-20 border-b border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg glow-primary">
                        <i data-lucide="wifi"></i>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-500 to-purple-500">ISP Cloud</span>
                </div>
            </div>

            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-5rem)]">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 text-brand-600 dark:text-brand-500 bg-brand-50 dark:bg-brand-500/10 rounded-xl transition-all font-medium">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/analytics') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                    <i data-lucide="bar-chart-2" class="w-5 h-5"></i> AI Analytics
                </a>
                <a href="{{ url('/gis') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                    <i data-lucide="map" class="w-5 h-5"></i> GIS Fiber Map
                </a>
                <a href="{{ url('/customers') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                    <i data-lucide="users" class="w-5 h-5"></i> Customers
                </a>
                <a href="{{ url('/routers') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                    <i data-lucide="router" class="w-5 h-5"></i> Routers
                </a>
                <a href="{{ url('/billing') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                    <i data-lucide="credit-card" class="w-5 h-5"></i> Billing
                </a>
                
                <div class="pt-6 mt-6 border-t border-gray-200 dark:border-dark-border">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Settings</p>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-dark-border/50 rounded-xl transition-all font-medium">
                        <i data-lucide="settings" class="w-5 h-5"></i> Configuration
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 w-full overflow-hidden">
            
            <!-- Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white/80 dark:bg-dark-surface/80 border-b border-gray-200 dark:border-dark-border glass-card z-10 sticky top-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="p-2 mr-4 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 dark:hover:bg-dark-border">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="relative hidden sm:block">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                        <input type="text" placeholder="Search customers, IP..." class="w-64 pl-10 pr-4 py-2 bg-gray-100 dark:bg-dark-bg border-transparent rounded-xl text-sm focus:bg-white dark:focus:bg-dark-bg focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none text-gray-700 dark:text-gray-200 placeholder-gray-400">
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-border rounded-xl transition-colors">
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5"></i>
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5" x-cloak></i>
                    </button>

                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-border rounded-xl transition-colors">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                            <img src="https://ui-avatars.com/api/?name=Super+Admin&background=6366F1&color=fff" alt="Avatar" class="w-9 h-9 rounded-xl shadow-sm border border-gray-200 dark:border-dark-border">
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold leading-none">Super Admin</p>
                                <p class="text-xs text-gray-500 mt-1">Administrator</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden md:block"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-surface rounded-xl shadow-lg border border-gray-200 dark:border-dark-border py-2 z-50" x-cloak>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-border">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-border">Settings</a>
                            <div class="border-t border-gray-200 dark:border-dark-border my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 dark:bg-[#0B0F19] p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Initialize Icons -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
