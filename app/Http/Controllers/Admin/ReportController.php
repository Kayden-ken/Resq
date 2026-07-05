<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\IncidentResponder;
use App\Models\EmergencyType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show reports index
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subDays(30);
        $endDate = $request->end_date ?? Carbon::now();

        // Summary stats
        $totalRequests = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgResponseTime = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('accepted_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, accepted_at)) as avg')
            ->value('avg');

        // Incidents by type
        $incidentsByType = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('emergency_type_id, count(*) as count')
            ->groupBy('emergency_type_id')
            ->with('emergencyType')
            ->get();

        // Daily incidents
        $dailyIncidents = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown
        $statusBreakdown = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 'totalRequests', 'avgResponseTime',
            'incidentsByType', 'dailyIncidents', 'statusBreakdown'
        ));
    }

    /**
     * Response time report
     */
    public function responseTime(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subDays(30);
        $endDate = $request->end_date ?? Carbon::now();

        $requests = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('accepted_at')
            ->with(['emergencyType', 'requester'])
            ->selectRaw('*, TIMESTAMPDIFF(MINUTE, created_at, accepted_at) as response_time_minutes')
            ->orderBy('response_time_minutes', 'desc')
            ->paginate(30);

        $avgTime = EmergencyRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('accepted_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, accepted_at)) as avg')
            ->value('avg');

        return view('admin.reports.response-time', compact('requests', 'avgTime', 'startDate', 'endDate'));
    }

    /**
     * Incidents report
     */
    public function incidents(Request $request)
    {
        $type = $request->type;
        $status = $request->status;

        $query = EmergencyRequest::with(['emergencyType', 'requester']);

        if ($type) {
            $query->where('emergency_type_id', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(30);
        $types = EmergencyType::all();

        return view('admin.reports.incidents', compact('requests', 'types'));
    }

    /**
     * Responder performance
     */
    public function responderPerformance(Request $request)
    {
        $responders = IncidentResponder::with('responder.user')
            ->selectRaw('responder_id,
                COUNT(*) as total_assignments,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                AVG(CASE WHEN accepted_at IS NOT NULL AND arrived_at IS NOT NULL
                    THEN TIMESTAMPDIFF(MINUTE, accepted_at, arrived_at) ELSE NULL END) as avg_arrival_time')
            ->groupBy('responder_id')
            ->orderByDesc('total_assignments')
            ->paginate(20);

        return view('admin.reports.responders', compact('responders'));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        // In production, use Laravel Excel
        return redirect()->back()->with('info', 'Export feature - implement with Laravel Excel');
    }
}