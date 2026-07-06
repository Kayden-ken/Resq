<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Dashboard - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                <div class="flex items-center gap-2">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center">
                            <i class="fas fa-shield-heart text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-slate-800">ResQ</span>
                    </a>
                    <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Responder
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('responder.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-700 hover:text-red-500 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Assignments</p>
                        <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Active Assignments</p>
                        <p class="text-3xl font-bold text-slate-800">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Completed</p>
                        <p class="text-3xl font-bold text-slate-800">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Two Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- My Assignments -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-800">
                        <i class="fas fa-tasks mr-2 text-blue-600"></i>My Assignments
                    </h2>
                </div>
                <div class="p-6">
                    @if($assignedRequests->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($assignedRequests as $req)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <span class="font-semibold text-slate-800">{{ $req->incident_number }}</span>
                                            <span class="ml-2 px-2 py-1 text-xs rounded-full
                                                @switch($req->status)
                                                    @case('pending') bg-yellow-100 text-yellow-700 @break
                                                    @case('accepted') bg-blue-100 text-blue-700 @break
                                                    @case('responding') bg-purple-100 text-purple-700 @break
                                                    @case('arrived') bg-green-100 text-green-700 @break
                                                    @case('completed') bg-gray-100 text-gray-700 @break
                                                    @default bg-red-100 text-red-700
                                                @endswitch">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-500">{{ $req->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-2">{{ $req->emergencyType?->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-slate-500 mb-3">{{ $req->address ?? 'Location pending' }}</p>
                                    <form method="POST" action="{{ route('responder.updateStatus', $req->id) }}" class="flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                                            <option value="">Update Status</option>
                                            <option value="assigned">Assigned</option>
                                            <option value="en_route">En Route</option>
                                            <option value="arrived">Arrived</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clipboard-check text-slate-400 text-2xl"></i>
                            </div>
                            <p class="text-slate-500">No assignments yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Requests -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-800">
                        <i class="fas fa-bell mr-2 text-red-500"></i>Available Requests
                    </h2>
                </div>
                <div class="p-6">
                    @if($availableRequests->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($availableRequests as $req)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <span class="font-semibold text-slate-800">{{ $req->incident_number }}</span>
                                            @if($req->is_sos)
                                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                                                    SOS
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-500">{{ $req->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-2">{{ $req->emergencyType?->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-slate-500 mb-3">{{ $req->address ?? 'Location pending' }}</p>
                                    <form method="POST" action="{{ route('responder.accept', $req->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full gradient-bg text-white font-semibold py-2 px-4 rounded-lg hover:opacity-90 transition text-sm">
                                            <i class="fas fa-check mr-2"></i>Accept Request
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-slate-400 text-2xl"></i>
                            </div>
                            <p class="text-slate-500">No available requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>