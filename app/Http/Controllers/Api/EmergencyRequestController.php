<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\EmergencyType;
use App\Models\EmergencyAgency;
use App\Models\IncidentHistory;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmergencyRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all emergency types
     */
    public function getTypes()
    {
        $types = EmergencyType::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Get all emergency agencies
     */
    public function getAgencies()
    {
        $agencies = EmergencyAgency::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $agencies
        ]);
    }

    /**
     * Create emergency request
     */
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emergency_type_id' => ['required', 'exists:emergency_types,id'],
            'description' => ['required', 'string', 'max:2000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_sos' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check for false report detection
        $recentRequestCount = EmergencyRequest::where('requester_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $isVerified = $recentRequestCount < 3;
        $needsVerification = $request->is_sos && $recentRequestCount >= 3;

        $emergencyRequest = EmergencyRequest::create([
            'requester_id' => $user->id,
            'emergency_type_id' => $request->emergency_type_id,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address ?? $this->reverseGeocode($request->latitude, $request->longitude),
            'status' => $needsVerification ? 'pending_verification' : 'pending',
            'is_sos' => $request->is_sos ?? false,
            'is_verified' => $isVerified,
            'incident_number' => 'INC-' . strtoupper(Str::random(8)),
        ]);

        // Create incident history
        IncidentHistory::create([
            'emergency_request_id' => $emergencyRequest->id,
            'status' => $emergencyRequest->status,
            'notes' => 'Emergency request created',
            'created_by' => $user->id,
        ]);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($emergencyRequest);

        // Log the request
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'emergency.request.created',
            'description' => 'Emergency request created: ' . $emergencyRequest->incident_number,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $needsVerification ? 'SOS request requires verification' : 'Emergency request submitted',
            'data' => $emergencyRequest->load(['emergencyType', 'requester'])
        ], 201);
    }

    /**
     * SOS Quick Alert - Simplified emergency request
     */
    public function sosAlert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'emergency_type_id' => ['nullable', 'exists:emergency_types,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Get default emergency type or use provided
        $emergencyTypeId = $request->emergency_type_id ?? EmergencyType::where('code', 'medical')->first()?->id;

        // Check recent requests for false report detection
        $recentRequestCount = EmergencyRequest::where('requester_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $isVerified = $recentRequestCount < 3;

        $emergencyRequest = EmergencyRequest::create([
            'requester_id' => $user->id,
            'emergency_type_id' => $emergencyTypeId,
            'description' => 'SOS Alert - Immediate assistance required',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $this->reverseGeocode($request->latitude, $request->longitude),
            'status' => $isVerified ? 'pending' : 'pending_verification',
            'is_sos' => true,
            'is_verified' => $isVerified,
            'incident_number' => 'SOS-' . strtoupper(Str::random(6)),
        ]);

        // Create incident history
        IncidentHistory::create([
            'emergency_request_id' => $emergencyRequest->id,
            'status' => $emergencyRequest->status,
            'notes' => 'SOS Alert triggered',
            'created_by' => $user->id,
        ]);

        // High priority notification
        $this->notificationService->sendEmergencyAlert($emergencyRequest);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($emergencyRequest);

        // Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'emergency.sos.created',
            'description' => 'SOS Alert: ' . $emergencyRequest->incident_number,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOS Alert sent! Help is on the way.',
            'data' => $emergencyRequest->load(['emergencyType'])
        ], 201);
    }

    /**
     * Get user's emergency requests
     */
    public function index(Request $request)
    {
        $requests = EmergencyRequest::with(['emergencyType', 'responders.user'])
            ->where('requester_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Get single emergency request
     */
    public function show(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::with([
            'emergencyType',
            'requester.profile',
            'responders.user',
            'media',
            'history' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        // Check ownership or admin access
        if ($emergencyRequest->requester_id !== $request->user()->id &&
            !$request->user()->canAccessAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $emergencyRequest
        ]);
    }

    /**
     * Update emergency request status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'in:pending,accepted,responding,arrived,completed,cancelled,rejected'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $emergencyRequest = EmergencyRequest::find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        $oldStatus = $emergencyRequest->status;
        $emergencyRequest->update(['status' => $request->status]);

        // Create history entry
        IncidentHistory::create([
            'emergency_request_id' => $emergencyRequest->id,
            'status' => $request->status,
            'notes' => $request->notes ?? "Status changed from {$oldStatus} to {$request->status}",
            'created_by' => $request->user()->id,
        ]);

        // Notify requester of status change
        if ($request->user()->isResponder()) {
            $this->notificationService->notifyStatusChange($emergencyRequest);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $emergencyRequest->fresh(['emergencyType', 'responders'])
        ]);
    }

    /**
     * Update requester location
     */
    public function updateLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $emergencyRequest = EmergencyRequest::where('requester_id', $request->user()->id)->find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        $emergencyRequest->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address ?? $emergencyRequest->address,
            'last_location_update' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
            'data' => $emergencyRequest
        ]);
    }

    /**
     * Track emergency request (for requester)
     */
    public function trackRequest(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::with([
            'emergencyType',
            'responders.user',
            'history' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ])->find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        if ($emergencyRequest->requester_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $emergencyRequest,
                'status' => $emergencyRequest->status,
                'responders' => $emergencyRequest->responders,
                'timeline' => $emergencyRequest->history
            ]
        ]);
    }

    /**
     * Cancel emergency request
     */
    public function cancelRequest(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::where('requester_id', $request->user()->id)->find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        if (in_array($emergencyRequest->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel this request'
            ], 400);
        }

        $emergencyRequest->update(['status' => 'cancelled']);

        // Create history entry
        IncidentHistory::create([
            'emergency_request_id' => $emergencyRequest->id,
            'status' => 'cancelled',
            'notes' => $request->notes ?? 'Request cancelled by user',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled'
        ]);
    }

    /**
     * Notify emergency contacts
     */
    private function notifyEmergencyContacts($emergencyRequest)
    {
        $user = $emergencyRequest->requester;
        $contacts = $user->emergencyContacts;

        foreach ($contacts as $contact) {
            $this->notificationService->sendToContact($contact, $emergencyRequest);
        }
    }

    /**
     * Reverse geocode (placeholder - integrate with maps API)
     */
    private function reverseGeocode($lat, $lng)
    {
        // In production, use Google Maps Geocoding API
        return "Location ({$lat}, {$lng})";
    }
}