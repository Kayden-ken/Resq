@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Reports & Analytics</h1>
    <p class="text-gray-600">System performance and incident analysis</p>
</div>

<!-- Date Range Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Apply</button>
    </form>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Incidents</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalRequests }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Average Response Time</p>
        <p class="text-3xl font-bold text-gray-800">{{ round($avgResponseTime ?? 0, 1) }} <span class="text-sm font-normal">minutes</span></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Completion Rate</p>
        @php
        $completed = $statusBreakdown->where('status', 'completed')->first()->count ?? 0;
        $rate = $totalRequests > 0 ? round(($completed / $totalRequests) * 100, 1) : 0;
        @endphp
        <p class="text-3xl font-bold text-green-600">{{ $rate }}%</p>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-bold mb-4">Incidents by Type</h2>
        <canvas id="typeChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-bold mb-4">Daily Incidents</h2>
        <canvas id="dailyChart" height="200"></canvas>
    </div>
</div>

<!-- Status Breakdown -->
<div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-bold mb-4">Status Distribution</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($statusBreakdown as $status)
        <div class="text-center p-3 bg-gray-50 rounded">
            <p class="text-2xl font-bold">{{ $status->count }}</p>
            <p class="text-sm text-gray-600">{{ ucfirst($status->status) }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Quick Links -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.reports.response-time') }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md">
        <h3 class="font-bold"><i class="fas fa-clock text-blue-500"></i> Response Time Report</h3>
        <p class="text-sm text-gray-500 mt-1">Detailed response time analysis</p>
    </a>
    <a href="{{ route('admin.reports.incidents') }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md">
        <h3 class="font-bold"><i class="fas fa-list text-green-500"></i> Incident List</h3>
        <p class="text-sm text-gray-500 mt-1">All incidents with filters</p>
    </a>
    <a href="{{ route('admin.reports.responders') }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md">
        <h3 class="font-bold"><i class="fas fa-users text-purple-500"></i> Responder Performance</h3>
        <p class="text-sm text-gray-500 mt-1">Responder statistics</p>
    </a>
</div>

<script>
    const typeData = @json($incidentsByType);
    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: typeData.map(t => t.emergency_type?.name ?? 'Unknown'),
            datasets: [{
                label: 'Incidents',
                data: typeData.map(t => t.count),
                backgroundColor: '#3b82f6'
            }]
        },
        options: { indexAxis: 'y' }
    });

    const dailyData = @json($dailyIncidents);
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Incidents',
                data: dailyData.map(d => d.count),
                borderColor: '#10b981',
                tension: 0.3
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection