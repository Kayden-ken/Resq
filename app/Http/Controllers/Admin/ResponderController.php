<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyAgency;
use App\Models\Responder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ResponderController extends Controller
{
    /**
     * List all responders
     */
    public function index(Request $request)
    {
        $query = Responder::with('user.profile', 'agency');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->agency_id) {
            $query->where('agency_id', $request->agency_id);
        }

        $responders = $query->orderBy('created_at', 'desc')->paginate(20);
        $agencies = EmergencyAgency::where('is_active', true)->get();

        return view('admin.responders.index', compact('responders', 'agencies'));
    }

    /**
     * Show responder details
     */
    public function show($id)
    {
        $responder = Responder::with(['user.profile', 'agency', 'assignments.emergencyRequest.emergencyType'])
            ->findOrFail($id);

        return view('admin.responders.show', compact('responder'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $agencies = EmergencyAgency::where('is_active', true)->get();
        $users = User::where('user_type', 'user')
            ->whereDoesntHave('responderProfile')
            ->get();

        return view('admin.responders.create', compact('agencies', 'users'));
    }

    /**
     * Create new responder
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id', 'unique:responders,user_id'],
            'agency_id' => ['required', 'exists:emergency_agencies,id'],
            'badge_number' => ['nullable', 'string', 'max:50'],
            'vehicle_info' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $responder = Responder::create($request->all());

        // Update user type
        User::where('id', $request->user_id)->update(['user_type' => 'responder']);

        return redirect()->route('admin.responders')->with('success', 'Responder created successfully');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $responder = Responder::findOrFail($id);
        $agencies = EmergencyAgency::where('is_active', true)->get();

        return view('admin.responders.edit', compact('responder', 'agencies'));
    }

    /**
     * Update responder
     */
    public function update(Request $request, $id)
    {
        $responder = Responder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agency_id' => ['required', 'exists:emergency_agencies,id'],
            'badge_number' => ['nullable', 'string', 'max:50'],
            'vehicle_info' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $responder->update($request->all());

        return redirect()->route('admin.responders')->with('success', 'Responder updated successfully');
    }

    /**
     * Delete responder
     */
    public function destroy($id)
    {
        $responder = Responder::findOrFail($id);

        // Update user type back to user
        User::where('id', $responder->user_id)->update(['user_type' => 'user']);

        $responder->delete();

        return redirect()->route('admin.responders')->with('success', 'Responder deleted successfully');
    }
}