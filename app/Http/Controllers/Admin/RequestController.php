<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\Responder;
use App\Models\IncidentResponder;
use App\Models\EmergencyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * List all emergency requests
     */
    public function index(Request $request)
    {
        $query = EmergencyRequest::with(['emergencyType', 'requester', 'responders.user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('emergency_type_id', $request->type);
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        $types = EmergencyType::where('is_active', true)->get();

        return view('admin.requests.index', compact('requests', 'types'));
    }

    /**
     * Show request details
     */
    public function show($id)
    {
        $emergencyRequest = EmergencyRequest::with([
            'emergencyType',
            'requester.profile',
            'responders.user',
            'media',
            'history' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        $availableResponders = Responder::with(['user', 'agency'])
            ->where('status', Responder::STATUS_AVAILABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.requests.show', compact('emergencyRequest', 'availableResponders'));
    }

    /**
     * Update request
     */
    public function update(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => ['sometimes', 'string', 'in:pending,accepted,responding,arrived,completed,cancelled,rejected,pending_verification'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,critical'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['status', 'priority']);

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $emergencyRequest->update($data);

        return redirect()->back()->with('success', 'Request updated');
    }

    /**
     * Assign responder
     */
    public function assignResponder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'responder_id' => ['required', 'exists:responders,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $emergencyRequest = EmergencyRequest::findOrFail($id);

        // Check if already assigned
        $exists = IncidentResponder::where('emergency_request_id', $id)
            ->where('responder_id', $request->responder_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Responder already assigned');
        }

        // Create assignment
        IncidentResponder::create([
            'emergency_request_id' => $id,
            'responder_id' => $request->responder_id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // Update status
        if ($emergencyRequest->status === 'pending') {
            $emergencyRequest->update(['status' => 'accepted']);
        }

        return redirect()->back()->with('success', 'Responder assigned');
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = ['status' => $request->status];

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        EmergencyRequest::where('id', $id)->update($data);

        return redirect()->back()->with('success', 'Status updated');
    }

    /**
     * Verify request
     */
    public function verifyRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'verified' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $emergencyRequest = EmergencyRequest::findOrFail($id);

        $update = [
            'is_verified' => $request->verified,
            'verified_at' => $request->verified ? now() : null,
        ];

        if ($request->verified && $emergencyRequest->status === 'pending_verification') {
            $update['status'] = 'pending';
        }

        $emergencyRequest->update($update);

        return redirect()->back()->with('success', $request->verified ? 'Request verified' : 'Request rejected');
    }
}