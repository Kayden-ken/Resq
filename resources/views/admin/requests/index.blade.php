@extends('layouts.admin')

@section('title', 'Emergency Requests')

@section('content')
<!-- Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Emergency Requests</h1>
        <p class="text-slate-500">Manage all emergency incidents</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="responding" {{ request('status') == 'responding' ? 'selected' : '' }}>Responding</option>
                <option value="arrived" {{ request('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Type</label>
            <select name="type" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                <option value="">All Types</option>
                @foreach($types as $type)
                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
        </div>
        <button type="submit" class="gradient-bg text-white px-5 py-2.5 rounded-xl hover:opacity-90 transition font-medium">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('admin.requests') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="fas fa-times mr-2"></i>Clear
        </a>
    </form>
</div>

<!-- Requests Table -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Incident #</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Type</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Requester</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Location</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Status</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Date</th>
                <th class="text-left py-4 px-6 font-semibold text-slate-600">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="py-4 px-6 font-medium text-slate-800">{{ $request->incident_number }}</td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600">
                        {{ $request->emergencyType?->name ?? 'Unknown' }}
                    </span>
                </td>
                <td class="py-4 px-6 text-slate-600">{{ $request->requester->name ?? 'N/A' }}</td>
                <td class="py-4 px-6 text-slate-500 truncate max-w-[150px]">{{ $request->address ?? 'N/A' }}</td>
                <td class="py-4 px-6">
                    @php
                    $statusClass = match($request->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'accepted' => 'bg-blue-100 text-blue-700',
                        'responding' => 'bg-purple-100 text-purple-700',
                        'arrived' => 'bg-green-100 text-green-700',
                        'completed' => 'bg-slate-100 text-slate-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100'
                    };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                </td>
                <td class="py-4 px-6 text-slate-500">{{ $request->created_at->format('M d, H:i') }}</td>
                <td class="py-4 px-6 space-x-2">
                    <a href="{{ route('admin.requests.show', $request->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition" title="View Request">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.requests.show', $request->id) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 px-3 py-2 text-xs font-semibold hover:bg-blue-100 transition" title="Deploy Responder">
                        <i class="fas fa-rocket mr-2"></i>Deploy
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-slate-500">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <i class="fas fa-inbox text-2xl text-slate-300"></i>
                        </div>
                        <p class="text-lg font-medium">No requests found</p>
                        <p class="text-sm text-slate-400">Try adjusting your filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($requests->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection