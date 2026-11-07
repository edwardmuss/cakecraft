<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;

class EnsureOTPVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Get the authenticated user
            $user = Auth::user();
            // Retrieve settings
            $settings = SettingsHelper::getSettings();

            // Check if the user's OTP is verified
            if ($settings->otp_method && $settings->otp_method != 'none' && !$user->otp_verified_at) {
                // Logout the user
                Auth::logout();

                // Redirect to login page with a message
                return redirect()->route('login')->with('error', 'You need to verify your OTP before accessing this page.');
            }
        }

        return $next($request);
    }
}
