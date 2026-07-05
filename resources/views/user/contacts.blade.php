<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contacts - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow p-8">
            <h1 class="text-3xl font-bold text-red-500 mb-6">Emergency Contacts</h1>
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            <form method="POST" action="/contacts" class="mb-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Name</label>
                        <input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Relationship</label>
                        <input type="text" name="relationship" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                        <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Email</label>
                        <input type="email" name="email" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <input type="checkbox" name="is_primary" value="1" class="mr-2">
                    <label class="text-gray-700">Set as primary contact</label>
                </div>
                <button type="submit" class="mt-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Add Contact</button>
            </form>
            <div class="space-y-3">
                @foreach($contacts as $contact)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <strong>{{ $contact->name }}</strong>
                            @if($contact->is_primary)
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Primary</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $contact->relationship }} • {{ $contact->phone }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-6"><a href="/" class="text-red-500 hover:underline">Back to dashboard</a></div>
        </div>
    </div>
</body>
</html>
