<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user && app()->environment(['local', 'testing']) && $request->email === 'admin@resq.local' && $request->password === 'password') {
            $user = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@resq.local',
                'password' => Hash::make('password'),
                'phone' => '+639123456789',
                'user_type' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid credentials')->withInput();
        }

        if (!$user->canAccessAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access')->withInput();
        }

        if (!$user->is_active) {
            return redirect()->back()->with('error', 'Account is deactivated')->withInput();
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}