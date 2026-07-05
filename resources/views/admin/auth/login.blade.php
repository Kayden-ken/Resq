@extends('layouts.admin-auth')

@section('title', 'Admin Login')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Panel - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center">
                        <i class="fas fa-shield-heart text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-slate-800">ResQ</span>
                </a>
                <p class="text-slate-500 mt-2">Admin Dashboard</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Welcome Back</h2>
                    <p class="text-slate-500 mt-1">Sign in to manage the system</p>
                </div>

                @if(session('error'))
                    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none"
                                placeholder="admin@resq.local" required>
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
                                class="block w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all outline-none"
                                placeholder="••••••••" required>
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-slate-400 hover:text-slate-600 transition" id="eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-[1.02] shadow-lg shadow-red-500/30">
                        Sign In
                    </button>
                </form>

                <!-- Demo Info -->
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-sm text-slate-500 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Demo: admin@resq.local / password
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

    <!-- Right Panel - Image/Branding -->
    <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12 bg-slate-900">
        <div class="text-center text-white max-w-lg">
            <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-shield-alt text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold mb-4">Secure Admin Portal</h2>
            <p class="text-white/80 text-lg leading-8 mb-10">Access essential system controls and manage emergency operations with confidence.</p>
            <div class="grid gap-3 text-sm text-slate-200 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">User Management</div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Request Oversight</div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Responder Operations</div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">System Settings</div>
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