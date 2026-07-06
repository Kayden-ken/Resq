<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\EmergencyContact;
use App\Models\EmergencyRequest;
use App\Models\EmergencyType;
use App\Models\Facility;
use App\Models\MedicalInfo;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPageController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user()->load(['profile', 'medicalInfo', 'emergencyContacts']);
        return view('user.profile', compact('user'));
    }

    public function emergencyRequests(Request $request)
    {
        $requests = EmergencyRequest::where('requester_id', $request->user()->id)
            ->with(['emergencyType'])
            ->orderByDesc('created_at')
            ->get();
        return view('user.requests', compact('requests'));
    }

    public function emergencyForm(Request $request)
    {
        $emergencyTypes = EmergencyType::where('is_active', true)->orderBy('name')->get();
        return view('user.request-form', compact('emergencyTypes'));
    }

    public function storeEmergencyRequest(Request $request)
    {
        $request->validate([
            'emergency_type_id' => ['required', 'exists:emergency_types,id'],
            'description' => ['required', 'string', 'max:2000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'proof_image' => ['required', 'image', 'max:10240'], // 10MB max, required
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('emergency_proofs', $filename, 'public');
        }

        $emergencyRequest = EmergencyRequest::create([
            'requester_id' => $request->user()->id,
            'emergency_type_id' => $request->emergency_type_id,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address ?? 'Location captured',
            'status' => 'pending',
            'is_sos' => false,
            'is_verified' => true,
        ]);

        // Save proof image
        if ($imagePath) {
            Media::create([
                'incident_id' => $emergencyRequest->id,
                'uploader_id' => $request->user()->id,
                'media_type' => 'image',
                'file_name' => $request->file('proof_image')->getClientOriginalName(),
                'file_path' => $imagePath,
                'file_size' => $request->file('proof_image')->getSize(),
                'mime_type' => $request->file('proof_image')->getMimeType(),
                'description' => 'Proof image for emergency request',
            ]);
        }

        return redirect()->route('user.requests')->with('success', 'Emergency request submitted successfully.');
    }

    public function contacts(Request $request)
    {
        $contacts = $request->user()->emergencyContacts()->orderByDesc('is_primary')->get();
        return view('user.contacts', compact('contacts'));
    }

    public function addContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        if ($request->boolean('is_primary')) {
            $request->user()->emergencyContacts()->update(['is_primary' => false]);
        }

        $request->user()->emergencyContacts()->create($request->only(['name', 'relationship', 'phone', 'email', 'is_primary']));

        return redirect()->route('user.contacts')->with('success', 'Emergency contact added.');
    }

    public function facilities(Request $request)
    {
        $facilities = Facility::active()->orderBy('name')->get();
        return view('user.facilities', compact('facilities'));
    }

    public function announcements(Request $request)
    {
        $announcements = Announcement::active()->whereIn('target_audience', ['all', 'users'])->orderByDesc('created_at')->get();
        return view('user.announcements', compact('announcements'));
    }
}
