<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // For 'admin' role check
            if ($role === 'admin' && $user->role_id === 1) {
                return $next($request);
            }

            // For checking other roles if needed
            if ($user->role && $user->role->name === $role) {
                return $next($request);
            }
        }

        // Redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
}
