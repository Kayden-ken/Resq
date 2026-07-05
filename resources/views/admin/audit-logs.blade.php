@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Audit Logs</h1>
    <p class="text-gray-600">System activity history</p>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" class="flex gap-4">
        <input type="date" name="date" value="{{ request('date') }}" class="border rounded px-3 py-2">
        <input type="text" name="action" value="{{ request('action') }}" placeholder="Action search..." class="border rounded px-3 py-2 flex-1">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-4">User</th>
                <th class="text-left py-3 px-4">Action</th>
                <th class="text-left py-3 px-4">Description</th>
                <th class="text-left py-3 px-4">IP Address</th>
                <th class="text-left py-3 px-4">Date/Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">{{ $log->user->name ?? 'System' }}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs bg-gray-100 font-mono">{{ $log->action }}</span>
                </td>
                <td class="py-3 px-4 text-gray-600">{{ $log->description }}</td>
                <td class="py-3 px-4 text-gray-500">{{ $log->ip_address }}</td>
                <td class="py-3 px-4 text-gray-500">{{ $log->created_at->format('M d, H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">No audit logs found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $logs->links() }}
    </div>
</div>
@endsection