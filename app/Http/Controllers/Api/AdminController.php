<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmergencyRequest;
use App\Models\EmergencyType;
use App\Models\Responder;
use App\Models\AuditLog;
use App\Models\IncidentResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard(Request $request)
    {
        $totalRequests = EmergencyRequest::count();
        $pendingRequests = EmergencyRequest::where('status', 'pending')->count();
        $activeRequests = EmergencyRequest::whereIn('status', ['accepted', 'responding', 'arrived'])->count();
        $completedToday = EmergencyRequest::whereDate('completed_at', today())->count();
        $totalUsers = User::where('user_type', 'user')->count();
        $totalResponders = Responder::count();
        $availableResponders = Responder::where('status', 'available')->count();

        // Recent requests
        $recentRequests = EmergencyRequest::with(['emergencyType', 'requester'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Emergency type distribution
        $typeDistribution = EmergencyRequest::selectRaw('emergency_type_id, count(*) as count')
            ->groupBy('emergency_type_id')
            ->with('emergencyType')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_requests' => $totalRequests,
                    'pending_requests' => $pendingRequests,
                    'active_requests' => $activeRequests,
                    'completed_today' => $completedToday,
                    'total_users' => $totalUsers,
                    'total_responders' => $totalResponders,
                    'available_responders' => $availableResponders,
                ],
                'recent_requests' => $recentRequests,
                'type_distribution' => $typeDistribution,
            ]
        ]);
    }

    /**
     * Get all emergency requests (admin)
     */
    public function getRequests(Request $request)
    {
        $query = EmergencyRequest::with(['emergencyType', 'requester', 'responders.user']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Update emergency request (admin)
     */
    public function updateRequest(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::find($id);

        if (!$emergencyRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['sometimes', 'string', 'in:pending,accepted,responding,arrived,completed,cancelled,rejected,pending_verification'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,critical'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['status', 'priority']);

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $emergencyRequest->update($data);

        $description = 'Updated request ' . $emergencyRequest->incident_number . ': ' . ($request->notes ?? 'Status changed');

        // Log action
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.request_updated',
            'description' => $description,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request updated',
            'data' => $emergencyRequest->fresh(['emergencyType', 'requester'])
        ]);
    }

    /**
     * Assign responder to request
     */
    public function assignResponder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'responder_id' => ['required', 'exists:responders,id'],
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

        $responder = Responder::find($request->responder_id);

        // Check if already assigned
        $existingAssignment = IncidentResponder::where('emergency_request_id', $id)
            ->where('responder_id', $request->responder_id)
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Responder already assigned'
            ], 400);
        }

        // Create assignment
        $assignment = IncidentResponder::create([
            'emergency_request_id' => $id,
            'responder_id' => $request->responder_id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // Update request status if needed
        if ($emergencyRequest->status === 'pending') {
            $emergencyRequest->update(['status' => 'accepted']);
        }

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.responder_assigned',
            'description' => "Assigned responder {$responder->user->name} to {$emergencyRequest->incident_number}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Responder assigned',
            'data' => $assignment->load(['responder.user', 'emergencyRequest'])
        ]);
    }

    /**
     * Verify request (admin)
     */
    public function verifyRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'verified' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
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

        $emergencyRequest->update([
            'is_verified' => $request->verified,
            'verified_at' => $request->verified ? now() : null,
            'status' => $request->verified && $emergencyRequest->status === 'pending_verification'
                ? 'pending'
                : $emergencyRequest->status,
        ]);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $request->verified ? 'admin.request_verified' : 'admin.request_rejected',
            'description' => "Request {$emergencyRequest->incident_number} " . ($request->verified ? 'verified' : 'rejected'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->verified ? 'Request verified' : 'Request rejected',
            'data' => $emergencyRequest
        ]);
    }

    /**
     * Get all users
     */
    public function getUsers(Request $request)
    {
        $query = User::query()->with('profile');

        if ($request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['sometimes', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'User updated',
            'data' => $user->fresh(['profile'])
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Prevent deleting self
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 400);
        }

        $user->delete();

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.user_deleted',
            'description' => "Deleted user: {$user->email}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted'
        ]);
    }

    /**
     * Get all responders
     */
    public function getResponders(Request $request)
    {
        $query = Responder::with('user.profile');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->agency_id) {
            $query->where('agency_id', $request->agency_id);
        }

        $responders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $responders
        ]);
    }

    /**
     * Create responder
     */
    public function createResponder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id', 'unique:responders,user_id'],
            'agency_id' => ['required', 'exists:emergency_agencies,id'],
            'badge_number' => ['nullable', 'string', 'max:50'],
            'vehicle_info' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $responder = Responder::create($request->all());

        // Update user type
        User::where('id', $request->user_id)->update(['user_type' => 'responder']);

        // Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.responder_created',
            'description' => "Created responder: {$responder->user->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Responder created',
            'data' => $responder->load('user')
        ], 201);
    }

    /**
     * Update responder
     */
    public function updateResponder(Request $request, $id)
    {
        $responder = Responder::find($id);

        if (!$responder) {
            return response()->json([
                'success' => false,
                'message' => 'Responder not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'agency_id' => ['sometimes', 'exists:emergency_agencies,id'],
            'badge_number' => ['nullable', 'string', 'max:50'],
            'vehicle_info' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $responder->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Responder updated',
            'data' => $responder->fresh(['user'])
        ]);
    }

    /**
     * Get reports & analytics
     */
    public function getReports(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(30);
        $endDate = $request->end_date ?? now();

        // Response time analysis
        $avgResponseTime = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('accepted_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, accepted_at)) as avg_response_minutes')
            ->value('avg_response_minutes');

        // Incidents by type
        $incidentsByType = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('emergency_type_id, count(*) as count')
            ->groupBy('emergency_type_id')
            ->with('emergencyType')
            ->get();

        // Daily incident count
        $dailyIncidents = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status distribution
        $statusDistribution = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'avg_response_time_minutes' => round($avgResponseTime ?? 0, 2),
                'incidents_by_type' => $incidentsByType,
                'daily_incidents' => $dailyIncidents,
                'status_distribution' => $statusDistribution,
            ]
        ]);
    }

    /**
     * Export reports
     */
    public function exportReports(Request $request)
    {
        // In production, generate Excel/CSV
        return response()->json([
            'success' => true,
            'message' => 'Export functionality - implement with Laravel Excel'
        ]);
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->action) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get settings
     */
    public function getSettings(Request $request)
    {
        // Return system settings
        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => config('app.name'),
                'emergency_types' => EmergencyType::where('is_active', true)->get(),
            ]
        ]);
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        // In production, save to config/database
        return response()->json([
            'success' => true,
            'message' => 'Settings updated'
        ]);
    }

    /**
     * Create emergency type
     */
    public function createEmergencyType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:emergency_types'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = EmergencyType::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Emergency type created',
            'data' => $type
        ], 201);
    }

    /**
     * Update emergency type
     */
    public function updateEmergencyType(Request $request, $id)
    {
        $type = EmergencyType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency type not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $type->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Emergency type updated',
            'data' => $type
        ]);
    }

    /**
     * Delete emergency type
     */
    public function deleteEmergencyType(Request $request, $id)
    {
        $type = EmergencyType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency type not found'
            ], 404);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Emergency type deleted'
        ]);
    }
}