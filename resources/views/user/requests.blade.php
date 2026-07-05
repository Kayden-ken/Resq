<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-red-500">My Emergency Requests</h1>
                <a href="/requests/new" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">New Request</a>
            </div>
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if($requests->isEmpty())
                <p class="text-gray-500">No requests yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($requests as $request)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <strong>{{ $request->emergencyType->name ?? 'Emergency' }}</strong>
                                <span class="text-sm text-gray-500 uppercase">{{ $request->status }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $request->description }}</p>
                            <p class="text-sm text-gray-500 mt-2">Location: {{ $request->address ?? 'Pending' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-6"><a href="/" class="text-red-500 hover:underline">Back to dashboard</a></div>
        </div>
    </div>
</body>
</html>
