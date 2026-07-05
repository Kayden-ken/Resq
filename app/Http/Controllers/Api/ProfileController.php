<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\MedicalInfo;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        $user = $request->user()->load(['profile', 'medicalInfo', 'emergencyContacts']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user
        $user->update($request->only(['name', 'phone']));

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'date_of_birth', 'address', 'city', 'state', 'zip_code', 'profile_picture'
            ])
        );

        // Log profile update
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'profile.updated',
            'description' => 'User profile updated',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh(['profile', 'medicalInfo'])
        ]);
    }

    /**
     * Verify phone number
     */
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // In production, verify code from SMS
        // For demo, accept any 6-digit code
        $user = $request->user();
        $user->update(['phone_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully'
        ]);
    }

    /**
     * Get emergency contacts
     */
    public function getContacts(Request $request)
    {
        $contacts = $request->user()->emergencyContacts;

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    /**
     * Add emergency contact
     */
    public function addContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // If setting as primary, unset other primaries
        if ($request->is_primary) {
            $user->emergencyContacts()->update(['is_primary' => false]);
        }

        $contact = $user->emergencyContacts()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Emergency contact added',
            'data' => $contact
        ], 201);
    }

    /**
     * Update emergency contact
     */
    public function updateContact(Request $request, $id)
    {
        $contact = $request->user()->emergencyContacts()->find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'relationship' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If setting as primary, unset other primaries
        if ($request->is_primary) {
            $request->user()->emergencyContacts()->where('id', '!=', $id)->update(['is_primary' => false]);
        }

        $contact->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Contact updated',
            'data' => $contact
        ]);
    }

    /**
     * Delete emergency contact
     */
    public function deleteContact(Request $request, $id)
    {
        $contact = $request->user()->emergencyContacts()->find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted'
        ]);
    }

    /**
     * Get medical info
     */
    public function getMedicalInfo(Request $request)
    {
        $medicalInfo = $request->user()->medicalInfo;

        return response()->json([
            'success' => true,
            'data' => $medicalInfo
        ]);
    }

    /**
     * Update medical info
     */
    public function updateMedicalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'medications' => ['nullable', 'string', 'max:1000'],
            'medical_notes' => ['nullable', 'string', 'max:2000'],
            'organ_donor' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $user->medicalInfo()->updateOrCreate(
            ['user_id' => $user->id],
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Medical info updated',
            'data' => $user->medicalInfo
        ]);
    }
}