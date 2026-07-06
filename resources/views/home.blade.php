@extends('layouts.app')

@section('title', 'ResQ - Emergency Response System')

@section('content')
<!-- Navigation Bar -->
<nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center">
                        <i class="fas fa-shield-heart text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-slate-800">ResQ</span>
                </a>
            </div>

            <!-- Nav Links - Desktop -->
            <div class="hidden md:flex items-center gap-6">
                <a href="#features" class="text-sm font-medium text-slate-600 hover:text-red-500 transition">Features</a>
                <a href="#about" class="text-sm font-medium text-slate-600 hover:text-red-500 transition">About</a>
                <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-red-500 transition">Contact</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <span class="text-sm font-medium text-slate-600 hidden sm:block">Welcome, {{ $user?->name ?? 'User' }}</span>
                    <a href="{{ route('user.profile') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-red-500 transition">
                        <i class="fas fa-user-circle mr-1"></i> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-red-500 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-red-500 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white gradient-bg rounded-lg hover:opacity-90 transition">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <!-- Hero Header -->
    <header class="mb-10 rounded-3xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="relative gradient-bg p-8 sm:p-12">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-x-20 -translate-y-20"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-x-10 translate-y-10"></div>
            </div>

            <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="max-w-2xl text-center lg:text-left">
                    <div class="inline-flex items-center rounded-full bg-white/20 px-4 py-1.5 text-sm font-semibold text-white mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                        24/7 Emergency Support
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        Your Safety, Our Priority
                    </h1>
                    <p class="text-white/80 text-lg mb-6">
                        Access emergency services instantly. Request help, track responders, and stay connected with your loved ones during critical moments.
                    </p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                        <a href="{{ route('emergency') }}" class="px-6 py-3 bg-white text-red-600 font-semibold rounded-xl hover:bg-gray-100 transition shadow-lg">
                            <i class="fas fa-bolt mr-2"></i>Request Emergency
                        </a>
                        <a href="#features" class="px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 transition">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="w-64 h-64 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-6xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-100 border-t border-slate-100">
            <div class="p-4 text-center">
                <div class="text-2xl font-bold text-red-500">24/7</div>
                <div class="text-sm text-slate-500">Support</div>
            </div>
            <div class="p-4 text-center">
                <div class="text-2xl font-bold text-red-500">&lt;5min</div>
                <div class="text-sm text-slate-500">Response Time</div>
            </div>
            <div class="p-4 text-center">
                <div class="text-2xl font-bold text-red-500">100%</div>
                <div class="text-sm text-slate-500">Coverage</div>
            </div>
            <div class="p-4 text-center">
                <div class="text-2xl font-bold text-red-500">Free</div>
                <div class="text-sm text-slate-500">Service</div>
            </div>
        </div>
    </header>

    <!-- Quick Access Grid -->
    <section class="mb-10" id="features">
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800">Quick Access</h2>
            <p class="text-slate-500 mt-2">Access essential services quickly</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Emergency Request -->
            <a href="{{ route('emergency') }}"
               class="group card-hover rounded-2xl border-2 border-red-100 bg-red-50 p-6 transition-all">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center text-white text-xl shadow-lg shadow-red-500/30">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">SOS</span>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">Request Emergency</h3>
                <p class="mt-2 text-sm text-slate-600">Submit a new incident request for medical, fire, police, or rescue assistance.</p>
            </a>

            <!-- Profile -->
            <a href="{{ auth()->check() ? route('user.profile') : route('login') }}"
               class="group card-hover rounded-2xl border border-slate-200 bg-white p-6 transition-all shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">My Profile</h3>
                <p class="mt-2 text-sm text-slate-600">View and update your personal details, medical information, and contacts.</p>
            </a>

            <!-- Track Requests -->
            <a href="{{ auth()->check() ? route('user.requests') : route('login') }}"
               class="group card-hover rounded-2xl border border-slate-200 bg-white p-6 transition-all shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">Track Requests</h3>
                <p class="mt-2 text-sm text-slate-600">Check the status of your recent emergency service requests.</p>
            </a>

            <!-- Emergency Contacts -->
            <a href="{{ auth()->check() ? route('user.contacts') : route('login') }}"
               class="group card-hover rounded-2xl border border-slate-200 bg-white p-6 transition-all shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas fa-address-book"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">Emergency Contacts</h3>
                <p class="mt-2 text-sm text-slate-600">Manage your trusted contacts for quick access during emergencies.</p>
            </a>

            <!-- Facilities -->
            <a href="{{ auth()->check() ? route('user.facilities') : route('login') }}"
               class="group card-hover rounded-2xl border border-slate-200 bg-white p-6 transition-all shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">Nearby Facilities</h3>
                <p class="mt-2 text-sm text-slate-600">Find hospitals, police stations, and fire stations near you.</p>
            </a>

            <!-- Announcements -->
            <a href="{{ auth()->check() ? route('user.announcements') : route('login') }}"
               class="group card-hover rounded-2xl border border-slate-200 bg-white p-6 transition-all shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800 group-hover:text-red-600 transition">Announcements</h3>
                <p class="mt-2 text-sm text-slate-600">Stay informed with important updates from emergency services.</p>
            </a>
        </div>
    </section>

    <!-- Authenticated Content -->
    @auth
    <section class="mb-10 grid gap-8 lg:grid-cols-2">
        <!-- Recent Requests -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Recent Requests</h3>
                <a href="{{ route('user.requests') }}" class="text-sm font-medium text-red-500 hover:text-red-600">View all</a>
            </div>
            @if($activeRequests->isNotEmpty())
                <div class="space-y-3">
                    @foreach($activeRequests as $request)
                        <div class="rounded-xl border border-slate-100 p-4 hover:border-red-200 hover:bg-red-50/30 transition">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg @switch($request->status)
                                        @case('pending') bg-yellow-100 text-yellow-600 @break
                                        @case('dispatched') bg-blue-100 text-blue-600 @break
                                        @case('arrived') bg-green-100 text-green-600 @break
                                        @case('completed') bg-gray-100 text-gray-600 @break
                                        @default bg-red-100 text-red-600 @endswitch flex items-center justify-center">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $request->emergencyType->name ?? 'Emergency' }}</p>
                                        <p class="text-sm text-slate-500">{{ $request->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wide rounded-full
                                    @switch($request->status)
                                        @case('pending') bg-yellow-100 text-yellow-700 @break
                                        @case('dispatched') bg-blue-100 text-blue-700 @break
                                        @case('arrived') bg-green-100 text-green-700 @break
                                        @case('completed') bg-gray-100 text-gray-700 @break
                                        @default bg-red-100 text-red-700 @endswitch">
                                    {{ $request->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-check text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-slate-500">You don't have any recent requests</p>
                    <a href="{{ route('user.requests.new') }}" class="mt-3 inline-block text-sm font-medium text-red-500 hover:text-red-600">
                        Make your first request <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @endif
        </div>

        <!-- Announcements -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Latest Announcements</h3>
                <a href="{{ route('user.announcements') }}" class="text-sm font-medium text-red-500 hover:text-red-600">View all</a>
            </div>
            @if($announcements->isNotEmpty())
                <div class="space-y-3">
                    @foreach($announcements as $announcement)
                        <div class="rounded-xl border border-slate-100 p-4 hover:border-red-200 hover:bg-red-50/30 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-500 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-slate-800 truncate">{{ $announcement->title }}</p>
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $announcement->content }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $announcement->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bullhorn text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-slate-500">No announcements at the moment</p>
                </div>
            @endif
        </div>
    </section>
    @endauth

    <!-- Emergency Types & Facilities -->
    <section class="grid gap-8 lg:grid-cols-2">
        <!-- Emergency Types -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Emergency Categories</h3>
            @if($emergencyTypes->isNotEmpty())
                <div class="grid grid-cols-2 gap-3">
                    @foreach($emergencyTypes as $type)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50">
                            <div class="w-10 h-10 rounded-lg gradient-bg text-white flex items-center justify-center flex-shrink-0">
                                @switch($type->name)
                                    @case('Medical') <i class="fas fa-heartbeat"></i> @break
                                    @case('Fire') <i class="fas fa-fire"></i> @break
                                    @case('Police') <i class="fas fa-shield-alt"></i> @break
                                    @case('Rescue') <i class="fas fa-life-ring"></i> @break
                                    @default <i class="fas fa-exclamation-triangle"></i>
                                @endswitch
                            </div>
                            <span class="font-medium text-slate-700 text-sm">{{ $type->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-6">Emergency categories will appear here</p>
            @endif
        </div>

        <!-- Nearby Facilities -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Nearby Facilities</h3>
            @if($facilities->isNotEmpty())
                <div class="space-y-3">
                    @foreach($facilities as $facility)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0">
                                @switch($facility->type)
                                    @case('hospital') <i class="fas fa-hospital"></i> @break
                                    @case('police_station') <i class="fas fa-building"></i> @break
                                    @case('fire_station') <i class="fas fa-fire-extinguisher"></i> @break
                                    @default <i class="fas fa-map-marker-alt"></i>
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-800 truncate">{{ $facility->name }}</p>
                                <p class="text-sm text-slate-500 truncate">{{ $facility->address ?? 'Address pending' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-6">Facility information will be shown here</p>
            @endif
        </div>
    </section>
</div>

<!-- Footer -->
<footer class="bg-slate-900 text-white mt-12">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4">
            <div class="col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center">
                        <i class="fas fa-shield-heart text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold">ResQ</span>
                </div>
                <p class="text-slate-400 max-w-md">
                    Your trusted emergency response system. Quick access to emergency services, real-time tracking, and 24/7 support when you need it most.
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Features</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    <li><a href="#" class="hover:text-white transition">Support</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Emergency</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Police: 911</a></li>
                    <li><a href="#" class="hover:text-white transition">Fire: 911</a></li>
                    <li><a href="#" class="hover:text-white transition">Ambulance: 911</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-8 pt-8 text-center text-slate-400 text-sm">
            <p>&copy; {{ date('Y') }} ResQ. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection