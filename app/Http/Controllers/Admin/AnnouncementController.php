<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * List all announcements
     */
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Create announcement
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Announcement::create([
            ...$request->all(),
            'created_by' => auth()->id(),
            'is_public' => $request->is_public ?? true,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.announcements')->with('success', 'Announcement created');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:announcement,alert,weather,disaster'],
            'is_public' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $announcement->update($request->all());

        return redirect()->route('admin.announcements')->with('success', 'Announcement updated');
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();
        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted');
    }
}