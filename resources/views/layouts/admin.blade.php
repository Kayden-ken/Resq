<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ResQ Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    @auth
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 fixed h-full bg-slate-800 text-white p-4 flex flex-col">
            <!-- Logo -->
            <div class="mb-6 px-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center">
                        <i class="fas fa-shield-heart text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">ResQ</h1>
                        <p class="text-xs text-slate-400">Admin Panel</p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.requests') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.requests*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-exclamation-triangle w-5"></i>
                    <span>Emergency Requests</span>
                    <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">3</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.users*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.responders') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.responders*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-ambulance w-5"></i>
                    <span>Responders</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.reports*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('admin.announcements') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.announcements*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-bullhorn w-5"></i>
                    <span>Announcements</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.settings*') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
                <a href="{{ route('admin.audit-logs') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all {{ request()->routeIs('admin.audit-logs') ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                    <i class="fas fa-history w-5"></i>
                    <span>Audit Logs</span>
                </a>
            </nav>

            <!-- Footer Links -->
            <div class="mt-auto pt-4 border-t border-slate-700">
                <div class="space-y-2">
                    <a href="/" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-slate-300 transition hover:bg-slate-700 hover:text-white">
                        <i class="fas fa-globe w-5"></i>
                        <span>View Site</span>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl bg-slate-700 px-4 py-3 text-sm font-semibold text-red-300 transition hover:bg-red-900 hover:text-white">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-6">
            @yield('content')
        </main>
    </div>
    @else
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
        @yield('content')
    </div>
    @endauth

    <!-- Toast Notifications -->
    @if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-pulse">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-pulse">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif
</body>
</html>