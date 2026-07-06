<?php

namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\IncidentResponder;
use App\Models\EmergencyType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $responder = $request->user()->responderProfile;

        // Get assigned requests
        $assignedRequests = EmergencyRequest::whereHas('responders', function ($query) use ($responder) {
            $query->where('responder_id', $responder->id);
        })->with(['emergencyType', 'requester'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get available new requests
        $availableRequests = EmergencyRequest::where('status', 'pending')
            ->orWhere('status', 'accepted')
            ->with(['emergencyType', 'requester'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Get stats
        $stats = [
            'total' => IncidentResponder::where('responder_id', $responder->id)->count(),
            'completed' => IncidentResponder::where('responder_id', $responder->id)
                ->where('status', 'completed')->count(),
            'active' => IncidentResponder::where('responder_id', $responder->id)
                ->whereIn('status', ['assigned', 'en_route', 'arrived'])->count(),
        ];

        return view('responder.dashboard', compact('assignedRequests', 'availableRequests', 'stats'));
    }

    public function acceptRequest(Request $request, $id)
    {
        $responder = $request->user()->responderProfile;

        $emergencyRequest = EmergencyRequest::findOrFail($id);

        // Check if already assigned
        $exists = IncidentResponder::where('emergency_request_id', $id)
            ->where('responder_id', $responder->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Already assigned to this request');
        }

        // Create assignment
        IncidentResponder::create([
            'emergency_request_id' => $id,
            'responder_id' => $responder->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // Update request status
        if ($emergencyRequest->status === 'pending') {
            $emergencyRequest->update(['status' => 'accepted']);
        }

        return redirect()->back()->with('success', 'Request accepted!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:assigned,en_route,arrived,completed'],
        ]);

        $responder = $request->user()->responderProfile;

        $assignment = IncidentResponder::where('emergency_request_id', $id)
            ->where('responder_id', $responder->id)
            ->firstOrFail();

        $assignment->update(['status' => $request->status]);

        // Update emergency request status
        $emergencyRequest = EmergencyRequest::findOrFail($id);
        $statusMap = [
            'assigned' => 'accepted',
            'en_route' => 'responding',
            'arrived' => 'arrived',
            'completed' => 'completed',
        ];
        $emergencyRequest->update(['status' => $statusMap[$request->status]]);

        return redirect()->back()->with('success', 'Status updated!');
    }
}