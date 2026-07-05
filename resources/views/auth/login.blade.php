@extends('layouts.app')

@section('title', 'Login - ResQ')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Panel - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
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

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Welcome back</h2>
                    <p class="text-slate-500 mt-1">Sign in to your account to continue</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

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
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-slate-400 hover:text-slate-600 transition" id="eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-red-500 focus:ring-red-500">
                            <span class="ml-2 text-sm text-slate-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm font-medium text-red-500 hover:text-red-600">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-[1.02] shadow-lg shadow-red-500/30">
                        Sign In
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

                <!-- Register Link -->
                <p class="text-center text-slate-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-red-500 hover:text-red-600 transition">
                        Create one
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

    <!-- Right Panel - Image/Branding -->
    <div class="hidden lg:flex lg:w-1/2 gradient-bg items-center justify-center p-12">
        <div class="text-center text-white max-w-lg">
            <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-hand-holding-heart text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold mb-4">Your Safety, Our Priority</h2>
            <p class="text-white/80 text-lg mb-8">Access emergency services quickly, manage your profile, and stay connected with responders when you need them most.</p>
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <div class="bg-white/10 rounded-lg px-4 py-2">
                    <i class="fas fa-bolt mr-2"></i>Quick Response
                </div>
                <div class="bg-white/10 rounded-lg px-4 py-2">
                    <i class="fas fa-location-dot mr-2"></i>Live Tracking
                </div>
                <div class="bg-white/10 rounded-lg px-4 py-2">
                    <i class="fas fa-users mr-2"></i>24/7 Support
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
@endsection