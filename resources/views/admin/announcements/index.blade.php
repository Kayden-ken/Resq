@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Announcements</h1>
        <p class="text-gray-600">Broadcast messages to users</p>
    </div>
    <a href="{{ route('admin.announcements.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus"></i> New Announcement
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-4">Title</th>
                <th class="text-left py-3 px-4">Type</th>
                <th class="text-left py-3 px-4">Public</th>
                <th class="text-left py-3 px-4">Active</th>
                <th class="text-left py-3 px-4">Expires</th>
                <th class="text-left py-3 px-4">Created</th>
                <th class="text-left py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $announcement)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4 font-medium">{{ $announcement->title }}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs bg-gray-100">{{ ucfirst($announcement->type) }}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs {{ $announcement->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100' }}">
                        {{ $announcement->is_public ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs {{ $announcement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-500">
                    {{ $announcement->expires_at ? $announcement->expires_at->format('M d, Y') : 'Never' }}
                </td>
                <td class="py-3 px-4 text-gray-500">{{ $announcement->created_at->format('M d, Y') }}</td>
                <td class="py-3 px-4">
                    <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="text-blue-500 hover:underline mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement->id) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-500">No announcements</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $announcements->links() }}
    </div>
</div>
@endsection