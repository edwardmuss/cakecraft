<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session()->get('last_activity');
            $sessionLifetime = config('session.lifetime') * 60; // in seconds

            if ($lastActivity && now()->timestamp - $lastActivity > $sessionLifetime) {
                Auth::logout();  // Logout if session expired
                return response()->json(['authenticated' => false], 401); // Return a JSON response
            }

            // Update last activity time only if the session hasn't expired
            session()->put('last_activity', now()->timestamp);
        }

        return $next($request);
    }
}
