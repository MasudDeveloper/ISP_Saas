<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ISP SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb' },
                        dark: { bg: '#0f172a', surface: '#1e293b', border: '#334155' }
                    }
                }
            }
        }
    </script>
    <style>
        .glow-primary { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-dark-bg text-gray-900 dark:text-white min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Elements -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg glow-primary mb-4">
                <i data-lucide="wifi" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold">ISP Central Admin</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sign in to manage your network</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-dark-surface p-8 rounded-2xl border border-gray-200 dark:border-dark-border shadow-xl">
            <form action="{{ url('/login') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-3 rounded-xl text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admin Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 dark:text-white outline-none transition-all" placeholder="admin@isp.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <a href="#" class="text-xs text-brand-600 dark:text-brand-400 hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 dark:text-white outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-brand-500 hover:bg-brand-600 text-white font-medium rounded-xl transition-all shadow-lg glow-primary flex items-center justify-center gap-2">
                    Sign In
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
