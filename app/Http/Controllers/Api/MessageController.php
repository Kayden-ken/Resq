<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\Message;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get messages for a request
     */
    public function getMessages(Request $request, $requestId)
    {
        $emergencyRequest = EmergencyRequest::find($requestId);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        // Check authorization
        $user = $request->user();
        $isRequester = $emergencyRequest->requester_id === $user->id;
        $isResponder = false;
        $isAdmin = $user->canAccessAdmin();

        if (!$isRequester && !$isAdmin) {
            // Check if user is assigned responder
            $assignedResponder = $emergencyRequest->responders()
                ->where('user_id', $user->id)
                ->exists();
            $isResponder = $assignedResponder;
        }

        if (!$isRequester && !$isResponder && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $messages = Message::with('sender')
            ->where('emergency_request_id', $requestId)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read
        Message::where('emergency_request_id', $requestId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emergency_request_id' => ['required', 'exists:emergency_requests,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $emergencyRequest = EmergencyRequest::find($request->emergency_request_id);

        $user = $request->user();
        $isRequester = $emergencyRequest->requester_id === $user->id;
        $isAdmin = $user->canAccessAdmin();

        // Determine receiver
        $receiverId = null;
        if ($isRequester) {
            // Requester messaging - get assigned responder
            $assignedResponder = $emergencyRequest->responders()->first();
            $receiverId = $assignedResponder?->user_id;
        } else {
            // Responder or admin messaging - send to requester
            $receiverId = $emergencyRequest->requester_id;
        }

        if (!$receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'No recipient available'
            ], 400);
        }

        $message = Message::create([
            'emergency_request_id' => $request->emergency_request_id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $message->load('sender')
        ], 201);
    }

    /**
     * Get all conversations
     */
    public function getConversations(Request $request)
    {
        $user = $request->user();

        // Get unique emergency requests where user has messages
        $conversationRequests = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->select('emergency_request_id')
            ->distinct()
            ->pluck('emergency_request_id');

        $conversations = EmergencyRequest::with([
            'emergencyType',
            'requester',
            'responders.user'
        ])
            ->whereIn('id', $conversationRequests)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($req) use ($user) {
                // Get last message
                $lastMessage = Message::where('emergency_request_id', $req->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Get unread count
                $unreadCount = Message::where('emergency_request_id', $req->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'request' => $req,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }
}