<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * List all users
     */
    public function index(Request $request)
    {
        $query = User::query()->with('profile');

        if ($request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $user = User::with(['profile', 'medicalInfo', 'emergencyContacts', 'emergencyRequests.emergencyType'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', 'string', 'in:user,responder,dispatcher,admin'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'user_type' => $request->user_type,
            'is_active' => true,
        ]);

        // Create profile and medical info
        $user->profile()->create();
        $user->medicalInfo()->create();

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', 'string', 'in:user,responder,dispatcher,admin'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'phone', 'user_type', 'is_active']));

        if ($request->password) {
            $user->update(['password' => $request->password]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}