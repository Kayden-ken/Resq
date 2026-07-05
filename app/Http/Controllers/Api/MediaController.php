<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Upload media file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emergency_request_id' => ['required', 'exists:emergency_requests,id'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'type' => ['required', 'string', 'in:photo,video,audio,document'],
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

        // Check authorization
        if ($emergencyRequest->requester_id !== $user->id && !$user->canAccessAdmin()) {
            $isResponder = $emergencyRequest->responders()
                ->where('user_id', $user->id)
                ->exists();
            if (!$isResponder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $file = $request->file('file');
        $path = $file->store('media/' . $emergencyRequest->id, 'public');

        $media = Media::create([
            'emergency_request_id' => $emergencyRequest->id,
            'user_id' => $user->id,
            'type' => $request->type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media uploaded',
            'data' => $media
        ], 201);
    }

    /**
     * Get media file
     */
    public function show(Request $request, $id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        $emergencyRequest = $media->emergencyRequest;
        $user = $request->user();

        // Check authorization
        if ($emergencyRequest->requester_id !== $user->id && !$user->canAccessAdmin()) {
            $isResponder = $emergencyRequest->responders()
                ->where('user_id', $user->id)
                ->exists();
            if (!$isResponder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // Return file URL or download
        $url = Storage::disk('public')->url($media->file_path);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $media->id,
                'type' => $media->type,
                'file_name' => $media->file_name,
                'file_size' => $media->file_size,
                'mime_type' => $media->mime_type,
                'url' => $url,
                'created_at' => $media->created_at
            ]
        ]);
    }

    /**
     * Delete media
     */
    public function delete(Request $request, $id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        $user = $request->user();

        // Only owner or admin can delete
        if ($media->user_id !== $user->id && !$user->canAccessAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($media->file_path);

        // Delete record
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted'
        ]);
    }
}