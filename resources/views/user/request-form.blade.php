@extends('layouts.app')

@section('title', 'Emergency Request - ResQ')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="mx-auto max-w-3xl px-4">
        <!-- Back Link -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-red-500 mb-4 transition">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center text-white">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Submit Emergency Request</h1>
                    <p class="text-slate-500 text-sm">No account required - Request urgent help now</p>
                </div>
            </div>

            <form method="POST" action="{{ route('emergency.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                @guest
                <!-- Guest Contact Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                    <h3 class="font-semibold text-yellow-800 mb-3"><i class="fas fa-user mr-2"></i>Your Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Your Name</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" placeholder="Enter your name" required>
                            @error('guest_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" placeholder="+63xxxxxxxxxx" required>
                            @error('guest_phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endguest

                <!-- Emergency Type -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Emergency Type</label>
                    <select name="emergency_type_id" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition" required>
                        <option value="">Select emergency type...</option>
                        @forelse($emergencyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @empty
                            <option value="">No emergency types available</option>
                        @endforelse
                    </select>
                    @error('emergency_type_id')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition" placeholder="Describe the emergency situation..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Proof Image -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Proof Image (Required)</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-red-400 transition" id="upload-area">
                        <input type="file" name="proof_image" id="proof_image" accept="image/*" class="hidden" required onchange="previewImage(event)">
                        <label for="proof_image" class="cursor-pointer">
                            <div id="preview-container" class="hidden mb-3">
                                <img id="image-preview" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-md">
                            </div>
                            <div id="upload-icon">
                                <i class="fas fa-camera text-3xl text-slate-400 mb-2"></i>
                                <p class="text-slate-500 text-sm">Click to upload photo proof</p>
                                <p class="text-slate-400 text-xs mt-1">JPG, PNG or GIF (max 10MB)</p>
                            </div>
                        </label>
                    </div>
                    @error('proof_image')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Section -->
                <div class="rounded-xl bg-slate-50 p-4 border border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-slate-700">Location</label>
                        <button type="button" onclick="detectLocation()" id="detect-btn" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white gradient-bg rounded-lg hover:opacity-90 transition">
                            <i class="fas fa-location-dot"></i> Use My Location
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div id="location-status" class="hidden mb-3 p-3 rounded-lg text-sm"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', '14.5995') }}"
                                class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition bg-white" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', '120.9842') }}"
                                class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition bg-white" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Address (Optional)</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition bg-white" placeholder="Enter address or use current location">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full gradient-bg text-white font-semibold py-4 px-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-[1.02] shadow-lg shadow-red-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Submit Emergency Request
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('preview-container').classList.remove('hidden');
            document.getElementById('upload-icon').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function detectLocation() {
    const statusDiv = document.getElementById('location-status');
    const btn = document.getElementById('detect-btn');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (!navigator.geolocation) {
        showStatus('Geolocation is not supported by your browser', 'error');
        return;
    }

    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
    showStatus('Requesting location access...', 'info');

    navigator.geolocation.getCurrentPosition(
        function(position) {
            latInput.value = position.coords.latitude.toFixed(6);
            lngInput.value = position.coords.longitude.toFixed(6);

            showStatus('Location detected successfully!', 'success');

            // Try to reverse geocode
            reverseGeocode(position.coords.latitude, position.coords.longitude);

            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-dot"></i> Use My Location';
        },
        function(error) {
            let message = 'Unable to detect location';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Location access denied. Please enable location services.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Location information unavailable.';
                    break;
                case error.TIMEOUT:
                    message = 'Location request timed out.';
                    break;
            }
            showStatus(message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-dot"></i> Use My Location';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function showStatus(message, type) {
    const statusDiv = document.getElementById('location-status');
    statusDiv.classList.remove('hidden');
    statusDiv.className = 'mb-3 p-3 rounded-lg text-sm';

    if (type === 'success') {
        statusDiv.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        statusDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
    } else if (type === 'error') {
        statusDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        statusDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
    } else {
        statusDiv.classList.add('bg-blue-50', 'text-blue-700', 'border', 'border-blue-200');
        statusDiv.innerHTML = '<i class="fas fa-info-circle mr-2"></i>' + message;
    }
}

function reverseGeocode(lat, lng) {
    const addressInput = document.getElementById('address');

    // Use Nominatim (OpenStreetMap) for reverse geocoding
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                addressInput.value = data.display_name;
            }
        })
        .catch(err => {
            console.log('Reverse geocoding failed:', err);
        });
}
</script>
@endsection