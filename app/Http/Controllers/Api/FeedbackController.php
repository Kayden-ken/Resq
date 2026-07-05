<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\Feedback;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Submit feedback for an incident
     */
    public function submitFeedback(Request $request, $requestId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'responder_id' => ['nullable', 'exists:responders,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $emergencyRequest = EmergencyRequest::find($requestId);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        // Check ownership
        if ($emergencyRequest->requester_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if request is completed
        if ($emergencyRequest->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only provide feedback for completed incidents'
            ], 400);
        }

        // Check if feedback already submitted
        $existingFeedback = Feedback::where('emergency_request_id', $requestId)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingFeedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback already submitted'
            ], 400);
        }

        $feedback = Feedback::create([
            'emergency_request_id' => $requestId,
            'user_id' => $request->user()->id,
            'responder_id' => $request->responder_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'feedback.submitted',
            'description' => "Feedback submitted for request {$emergencyRequest->incident_number}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted',
            'data' => $feedback
        ], 201);
    }

    /**
     * Get feedback for an incident
     */
    public function getFeedback(Request $request, $requestId)
    {
        $emergencyRequest = EmergencyRequest::find($requestId);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        // Check access
        $user = $request->user();
        $isRequester = $emergencyRequest->requester_id === $user->id;
        $isAdmin = $user->canAccessAdmin();

        if (!$isRequester && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $feedback = Feedback::where('emergency_request_id', $requestId)->get();

        return response()->json([
            'success' => true,
            'data' => $feedback
        ]);
    }
}