<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ResQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow p-8">
            <h1 class="text-3xl font-bold text-red-500 mb-6">My Profile</h1>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-semibold">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-semibold">{{ $user->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Blood Type</p>
                    <p class="font-semibold">{{ $user->medicalInfo->blood_type ?? 'Unknown' }}</p>
                </div>
            </div>
            <div class="mt-6">
                <a href="/" class="text-red-500 hover:underline">Back to dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
