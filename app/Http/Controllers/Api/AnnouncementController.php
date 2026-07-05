<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Get public announcements
     */
    public function publicIndex(Request $request)
    {
        $announcements = Announcement::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Get user announcements
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $announcements = Announcement::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('is_public', true)
                    ->orWhereHas('targetUsers', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Mark announcement as read
     */
    public function markAsRead(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        // In production, track read status in separate table
        return response()->json([
            'success' => true,
            'message' => 'Announcement marked as read'
        ]);
    }

    /**
     * Admin: Get all announcements
     */
    public function adminIndex(Request $request)
    {
        $query = Announcement::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Admin: Create announcement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:announcement,alert,weather,disaster'],
            'is_public' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $announcement = Announcement::create([
            ...$request->all(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created',
            'data' => $announcement
        ], 201);
    }

    /**
     * Admin: Update announcement
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', 'in:announcement,alert,weather,disaster'],
            'is_public' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $announcement->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated',
            'data' => $announcement
        ]);
    }

    /**
     * Admin: Delete announcement
     */
    public function destroy(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted'
        ]);
    }
}