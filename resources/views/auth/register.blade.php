@extends('layouts.app')

@section('title', 'Register - ResQ')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Panel - Image/Branding -->
    <div class="hidden lg:flex lg:w-1/2 gradient-bg items-center justify-center p-12">
        <div class="text-center text-white max-w-lg">
            <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-user-plus text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold mb-4">Join ResQ Today</h2>
            <p class="text-white/80 text-lg mb-8">Create your account to access emergency services, manage your profile, and connect with responders in your area.</p>
            <div class="space-y-4 text-left bg-white/10 rounded-xl p-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="text-white/90">Quick emergency request submission</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="text-white/90">Real-time request tracking</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="text-white/90">Manage emergency contacts</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="text-white/90">Access to nearby facilities</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2">
                    <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center">
                        <i class="fas fa-shield-heart text-white text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-slate-800">ResQ</span>
                </a>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Create Account</h2>
                    <p class="text-slate-500 mt-1">Fill in your details to get started</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-400"></i>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none @error('name') border-red-500 @enderror"
                                placeholder="John Doe" required>
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none @error('email') border-red-500 @enderror"
                                placeholder="you@example.com" required>
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-slate-400"></i>
                            </div>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none @error('phone') border-red-500 @enderror"
                                placeholder="+1 (555) 000-0000">
                        </div>
                        @error('phone')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input type="password" id="password" name="password"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none @error('password') border-red-500 @enderror"
                                placeholder="••••••••" required>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @else
                            <p class="mt-1.5 text-xs text-slate-400">Must be at least 8 characters</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" class="w-4 h-4 mt-0.5 rounded border-slate-300 text-red-500 focus:ring-red-500" required>
                        <label for="terms" class="ml-2 text-sm text-slate-600">
                            I agree to the <a href="#" class="text-red-500 hover:text-red-600 font-medium">Terms of Service</a> and <a href="#" class="text-red-500 hover:text-red-600 font-medium">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-[1.02] shadow-lg shadow-red-500/30">
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-500">Or</span>
                    </div>
                </div>

                <!-- Login Link -->
                <p class="text-center text-slate-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-red-500 hover:text-red-600 transition">
                        Sign in
                    </a>
                </p>
            </div>

            <!-- Back to Home -->
            <p class="text-center mt-6">
                <a href="/" class="text-sm text-slate-500 hover:text-slate-700 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to home
                </a>
            </p>
        </div>
    </div>
</div>
@endsection