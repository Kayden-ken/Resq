@extends('layouts.admin')

@section('title', 'Responders')

@section('content')
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:justify-between xl:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Emergency Responders</h1>
        <p class="text-gray-600">Manage responders and dispatch incidents from this pane.</p>
    </div>
    <a href="{{ route('admin.responders.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus"></i> Add Responder
    </a>
</div>


<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="busy" {{ request('status') == 'busy' ? 'selected' : '' }}>Busy</option>
                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="on_duty" {{ request('status') == 'on_duty' ? 'selected' : '' }}>On Duty</option>
            </select>
            <select name="agency_id" class="border rounded px-3 py-2">
                <option value="">All Agencies</option>
                @foreach($agencies as $agency)
                <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-3 px-4">Responder</th>
                    <th class="text-left py-3 px-4">Agency</th>
                    <th class="text-left py-3 px-4">Badge #</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Vehicle</th>
                    <th class="text-left py-3 px-4">Joined</th>
                    <th class="text-left py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($responders as $responder)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium">{{ $responder->user->name ?? 'N/A' }}</td>
                    <td class="py-3 px-4">{{ $responder->agency->name ?? 'N/A' }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $responder->badge_number ?? 'N/A' }}</td>
                    <td class="py-3 px-4">
                        @php
                        $statusClass = match($responder->status) {
                            'available' => 'bg-green-100 text-green-800',
                            'busy' => 'bg-red-100 text-red-800',
                            'offline' => 'bg-gray-100 text-gray-800',
                            'on_duty' => 'bg-blue-100 text-blue-800',
                            default => 'bg-gray-100'
                        };
                        @endphp
                        <span class="px-2 py-1 rounded text-xs {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $responder->status)) }}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $responder->vehicle_info ?? 'N/A' }}</td>
                    <td class="py-3 px-4 text-gray-500">{{ $responder->created_at->format('M d, Y') }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ route('admin.responders.show', $responder->id) }}" class="text-blue-500 hover:underline mr-2">View</a>
                        <a href="{{ route('admin.responders.edit', $responder->id) }}" class="text-green-500 hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-500">No responders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $responders->links() }}
        </div>
    </div>
</div>
@endsection