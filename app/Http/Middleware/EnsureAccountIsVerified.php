<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAccountIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check user is verified
        if ($user && is_null($user->registration_verified_at)) {

            // The OTP sign-up flow has been removed. Unverified accounts cannot enter.
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'This account is not verified.');
        }

        return $next($request);
    }
}
