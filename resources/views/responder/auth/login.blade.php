<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Login - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="w-full max-w-md px-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center">
                    <i class="fas fa-shield-heart text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-slate-800">ResQ</span>
            </a>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-shield text-2xl text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Responder Login</h1>
                <p class="text-slate-500 text-sm mt-1">Sign in to access your dashboard</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('responder.login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition" placeholder="responder@resq.com" required>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition" placeholder="Enter your password" required>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full gradient-bg text-white font-semibold py-4 px-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-[1.02] shadow-lg shadow-red-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-red-500">
                    <i class="fas fa-arrow-left mr-1"></i> Back to main login
                </a>
            </div>
        </div>
    </div>
</body>
</html>