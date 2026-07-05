<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\IncidentResponder;
use App\Models\Responder;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get responder status
     */
    public function getStatus(Request $request)
    {
        $responder = $request->user()->responderProfile;

        if (!$responder) {
            return response()->json([
                'success' => false,
                'message' => 'Responder profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $responder
        ]);
    }

    /**
     * Update responder status
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'in:available,busy,offline,on_duty'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $responder = $request->user()->responderProfile;

        if (!$responder) {
            return response()->json([
                'success' => false,
                'message' => 'Responder profile not found'
            ], 404);
        }

        $responder->update([
            'status' => $request->status,
            'current_latitude' => $request->latitude ?? $responder->current_latitude,
            'current_longitude' => $request->longitude ?? $responder->current_longitude,
        ]);

        // Log status change
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'responder.status_changed',
            'description' => "Responder status changed to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $responder
        ]);
    }

    /**
     * Get responder assignments
     */
    public function getAssignments(Request $request)
    {
        $responder = $request->user()->responderProfile;

        if (!$responder) {
            return response()->json([
                'success' => false,
                'message' => 'Responder profile not found'
            ], 404);
        }

        $assignments = IncidentResponder::with([
            'emergencyRequest.emergencyType',
            'emergencyRequest.requester.profile'
        ])
            ->where('responder_id', $responder->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Accept assignment
     */
    public function acceptAssignment(Request $request, $id)
    {
        $assignment = IncidentResponder::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        $responder = $request->user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($assignment->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Assignment already processed'
            ], 400);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Update emergency request status
        $emergencyRequest = $assignment->emergencyRequest;
        $emergencyRequest->update(['status' => 'accepted']);

        // Notify requester
        $this->notificationService->notifyStatusChange($emergencyRequest);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'responder.accepted',
            'description' => "Accepted assignment for {$emergencyRequest->incident_number}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment accepted',
            'data' => $assignment->fresh(['emergencyRequest'])
        ]);
    }

    /**
     * Reject assignment
     */
    public function rejectAssignment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = IncidentResponder::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        $responder = $request->user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($assignment->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Assignment already processed'
            ], 400);
        }

        $assignment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'responder.rejected',
            'description' => "Rejected assignment: {$request->reason}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment rejected'
        ]);
    }

    /**
     * Update assignment status (for responder)
     */
    public function updateAssignmentStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'in:en_route,arrived,completed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = IncidentResponder::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        $responder = $request->user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $statusMap = [
            'en_route' => 'responding',
            'arrived' => 'arrived',
            'completed' => 'completed',
        ];

        $assignment->update(['status' => $request->status]);

        // Update emergency request status
        $emergencyRequest = $assignment->emergencyRequest;
        $emergencyRequest->update(['status' => $statusMap[$request->status]]);

        // Update responder location if provided
        if ($request->latitude && $request->longitude) {
            $responder->update([
                'current_latitude' => $request->latitude,
                'current_longitude' => $request->longitude,
            ]);
        }

        // Notify requester
        $this->notificationService->notifyStatusChange($emergencyRequest);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'responder.status_update',
            'description' => "Updated assignment status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $assignment->fresh(['emergencyRequest'])
        ]);
    }

    /**
     * Update responder location
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

        $assignment = IncidentResponder::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        $responder = $request->user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $responder->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'last_location_update' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated'
        ]);
    }

    /**
     * Get nearby emergency facilities
     */
    public function getFacilities(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 50; // km

        if (!$latitude || !$longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Location required'
            ], 422);
        }

        // Get facilities within radius (simplified - use Haversine formula in production)
        $facilities = \App\Models\Facility::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $facilities
        ]);
    }
}