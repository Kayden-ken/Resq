@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Users</h1>
        <p class="text-gray-600">Manage all registered users</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
            class="border rounded px-3 py-2 flex-1">
        <select name="user_type" class="border rounded px-3 py-2">
            <option value="">All Types</option>
            <option value="user" {{ request('user_type') == 'user' ? 'selected' : '' }}>User</option>
            <option value="responder" {{ request('user_type') == 'responder' ? 'selected' : '' }}>Responder</option>
            <option value="dispatcher" {{ request('user_type') == 'dispatcher' ? 'selected' : '' }}>Dispatcher</option>
            <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Search</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-4">Name</th>
                <th class="text-left py-3 px-4">Email</th>
                <th class="text-left py-3 px-4">Phone</th>
                <th class="text-left py-3 px-4">Type</th>
                <th class="text-left py-3 px-4">Status</th>
                <th class="text-left py-3 px-4">Joined</th>
                <th class="text-left py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4 font-medium">{{ $user->name }}</td>
                <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                <td class="py-3 px-4 text-gray-600">{{ $user->phone ?? 'N/A' }}</td>
                <td class="py-3 px-4">
                    @php
                    $typeClass = match($user->user_type) {
                        'admin' => 'bg-red-100 text-red-800',
                        'dispatcher' => 'bg-purple-100 text-purple-800',
                        'responder' => 'bg-blue-100 text-blue-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                    @endphp
                    <span class="px-2 py-1 rounded text-xs {{ $typeClass }}">{{ ucfirst($user->user_type) }}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="py-3 px-4">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-500 hover:underline mr-2">View</a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-500 hover:underline">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-500">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $users->links() }}
    </div>
</div>
@endsection