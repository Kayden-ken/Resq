@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Header -->
<div class="mb-8 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-500">Emergency Response System Overview</p>
    </div>
    <div class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-600">
        <span class="mr-2 h-2.5 w-2.5 rounded-full bg-red-500"></span>
        Live operations center
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Total Requests</p>
                <p class="mt-1 text-3xl font-bold text-slate-800">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50">
                <i class="fas fa-exclamation-triangle text-xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Pending</p>
                <p class="mt-1 text-3xl font-bold text-yellow-600">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-yellow-50">
                <i class="fas fa-clock text-xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Active</p>
                <p class="mt-1 text-3xl font-bold text-red-600">{{ $stats['active_requests'] }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-red-50">
                <i class="fas fa-ambulance text-xl text-red-500"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Completed Today</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ $stats['completed_today'] }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-50">
                <i class="fas fa-check-circle text-xl text-green-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                <i class="fas fa-users text-slate-600"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Users</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                <i class="fas fa-user-md text-slate-600"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Responders</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_responders'] }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Available Responders</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['available_responders'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Incidents by Type</h2>
        <div class="h-64">
            <canvas id="typeChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Daily Trend (Last 7 Days)</h2>
        <div class="h-64">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Requests -->
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 p-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Recent Emergency Requests</h2>
            <p class="mt-1 text-sm text-slate-500">Latest incidents reported through the system</p>
        </div>
        <a href="{{ route('admin.requests') }}" class="text-sm font-medium text-red-500 hover:text-red-600">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Incident #</th>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Type</th>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Requester</th>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Status</th>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Date</th>
                    <th class="text-left py-4 px-6 font-semibold text-slate-600">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRequests as $request)
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="py-4 px-6 font-medium text-slate-800">{{ $request->incident_number }}</td>
                    <td class="py-4 px-6">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600">
                            {{ $request->emergencyType?->name ?? 'Unknown' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-600">{{ $request->requester->name ?? 'N/A' }}</td>
                    <td class="py-4 px-6">
                        @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'accepted' => 'bg-blue-100 text-blue-700',
                            'responding' => 'bg-purple-100 text-purple-700',
                            'arrived' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-slate-100 text-slate-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$request->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500">{{ $request->created_at->format('M d, H:i') }}</td>
                    <td class="py-4 px-6">
                        <a href="{{ route('admin.requests.show', $request->id) }}" class="text-red-500 hover:text-red-600">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-4xl text-slate-300 mb-2"></i>
                            <p>No recent requests</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Type Distribution Chart
    const typeData = @json($typeData);
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: typeData.map(t => t.name),
            datasets: [{
                data: typeData.map(t => t.count),
                backgroundColor: ['#dc2626', '#ea580c', '#2563eb', '#7c3aed', '#059669', '#d97706']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Trend Chart
    const trendData = @json($dailyTrend);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Incidents',
                data: trendData.map(d => d.count),
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection