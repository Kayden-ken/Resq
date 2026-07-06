<?php

namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('responder.auth.login');
    }

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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid credentials')->withInput();
        }

        if (!$user->isResponder()) {
            return redirect()->back()->with('error', 'Unauthorized access')->withInput();
        }

        if (!$user->is_active) {
            return redirect()->back()->with('error', 'Account is deactivated')->withInput();
        }

        Auth::login($user);

        return redirect()->route('responder.dashboard')->with('success', 'Welcome back!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('responder.login')->with('success', 'Logged out successfully');
    }
}