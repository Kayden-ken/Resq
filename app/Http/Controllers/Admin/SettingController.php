<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyType;
use App\Models\EmergencyAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Show settings
     */
    public function index()
    {
        $types = EmergencyType::orderBy('name')->get();
        $agencies = EmergencyAgency::orderBy('name')->get();

        return view('admin.settings.index', compact('types', 'agencies'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Save general settings
        // In production, store in database or config

        return redirect()->back()->with('success', 'Settings updated');
    }

    /**
     * Emergency types management
     */
    public function emergencyTypes()
    {
        $types = EmergencyType::orderBy('name')->get();
        return view('admin.settings.emergency-types', compact('types'));
    }

    /**
     * Create emergency type
     */
    public function storeEmergencyType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:emergency_types'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        EmergencyType::create($request->all());

        return redirect()->back()->with('success', 'Emergency type created');
    }

    /**
     * Update emergency type
     */
    public function updateEmergencyType(Request $request, $id)
    {
        $type = EmergencyType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $type->update($request->all());

        return redirect()->back()->with('success', 'Emergency type updated');
    }

    /**
     * Delete emergency type
     */
    public function deleteEmergencyType($id)
    {
        EmergencyType::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Emergency type deleted');
    }
}