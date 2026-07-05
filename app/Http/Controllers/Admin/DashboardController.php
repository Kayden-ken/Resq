<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\User;
use App\Models\Responder;
use App\Models\AuditLog;
use App\Models\EmergencyType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $stats = [
            'total_requests' => EmergencyRequest::count(),
            'pending_requests' => EmergencyRequest::where('status', 'pending')->count(),
            'active_requests' => EmergencyRequest::whereIn('status', ['accepted', 'responding', 'arrived'])->count(),
            'completed_today' => EmergencyRequest::whereDate('completed_at', today())->count(),
            'total_users' => User::where('user_type', 'user')->count(),
            'total_responders' => Responder::count(),
            'available_responders' => Responder::where('status', 'available')->count(),
        ];

        $recentRequests = EmergencyRequest::with(['emergencyType', 'requester'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Incidents by type for chart
        $typeData = EmergencyRequest::selectRaw('emergency_type_id, count(*) as count')
            ->groupBy('emergency_type_id')
            ->with('emergencyType')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->emergencyType?->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        // Daily trend (last 7 days)
        $dailyTrend = EmergencyRequest::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'typeData', 'dailyTrend'));
    }

    /**
     * Show audit logs
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->action) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.audit-logs', compact('logs'));
    }
}