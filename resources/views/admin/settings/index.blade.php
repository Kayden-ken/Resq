@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
    <p class="text-gray-600">Configure emergency types and agencies</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Emergency Types -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Emergency Types</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Icon</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-left py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                <tr class="border-b">
                    <td class="py-2">{{ $type->name }}</td>
                    <td class="py-2 text-xl">{{ $type->icon }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2">
                        <form method="POST" action="{{ route('admin.settings.emergency-types.delete', $type->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500">No types</td></tr>
                @endforelse
            </tbody>
        </table>

        <form method="POST" action="{{ route('admin.settings.emergency-types.store') }}" class="mt-4 pt-4 border-t">
            @csrf
            <h3 class="font-bold mb-2">Add New Type</h3>
            <div class="grid grid-cols-2 gap-2">
                <input type="text" name="name" placeholder="Name" class="border rounded px-2 py-1" required>
                <input type="text" name="code" placeholder="Code" class="border rounded px-2 py-1" required>
            </div>
            <button type="submit" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm">Add Type</button>
        </form>
    </div>

    <!-- Emergency Agencies -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-bold mb-4">Emergency Agencies</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Type</th>
                    <th class="text-left py-2">Phone</th>
                    <th class="text-left py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agencies as $agency)
                <tr class="border-b">
                    <td class="py-2">{{ $agency->name }}</td>
                    <td class="py-2">{{ ucfirst($agency->type) }}</td>
                    <td class="py-2">{{ $agency->phone }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $agency->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $agency->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500">No agencies</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection