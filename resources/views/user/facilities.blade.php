<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Facilities - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow p-8">
            <h1 class="text-3xl font-bold text-red-500 mb-6">Nearby Facilities</h1>
            <div class="space-y-3">
                @foreach($facilities as $facility)
                    <div class="border rounded-lg p-4">
                        <strong>{{ $facility->name }}</strong>
                        <p class="text-sm text-gray-600 mt-1">{{ $facility->address ?? 'Address not available' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Phone: {{ $facility->phone ?? 'N/A' }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-6"><a href="/" class="text-red-500 hover:underline">Back to dashboard</a></div>
        </div>
    </div>
</body>
</html>
