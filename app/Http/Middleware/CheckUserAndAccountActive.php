<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserAndAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Unauthenticated.');
        }

        // Pass unverified users to the EnsureAccountIsVerified middleware
        if (is_null($user->registration_verified_at)) {
            return $next($request);
        }

        // --- Standard suspension checks ---
        if (!$user->isActive) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is inactive. Please contact support.');
        }

        if (!$user->branch || !$user->branch->isActive) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'The associated company account is inactive.');
        }

        return $next($request);
    }
}
