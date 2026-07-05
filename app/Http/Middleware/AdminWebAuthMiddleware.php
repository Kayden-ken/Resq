<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminWebAuthMiddleware
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user has admin/dispatcher access for web dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        if (!$request->user()->canAccessAdmin()) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}