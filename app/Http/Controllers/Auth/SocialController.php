<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
        // return Socialite::driver($provider)->scopes(['email'])->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $userSocial = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Failed to login using ' . ucfirst($provider)]);
        }

        $email = $userSocial->getEmail();
        $user = User::where('email', $email)->first();

        if ($user) {
            // User exists! Check if they have an associated account
            if (!$user->branch_id) {
                // Edge case: User exists but no company profile
                session(['social_user' => ['name' => $user->name, 'email' => $email]]);
                return redirect()->route('register.complete_profile');
            }

            // Bypass OTP for social login
            $user->update(['otp_verified_at' => now(), 'otp' => null, 'otp_expires_at' => null]);
            Auth::login($user);
            return redirect()->route('root');
        } else {
            // User does NOT exist -> This is a new Social Signup!
            // Store their social info in the session
            session([
                'social_user' => [
                    'name'  => $userSocial->getName(),
                    'email' => $email,
                ]
            ]);

            // Redirect them to a form to complete the required fields (Company Name, Phone, etc.)
            return redirect()->route('register.complete_profile')
                ->with('info', 'Please complete your company profile to finish registration.');
        }
    }
}
