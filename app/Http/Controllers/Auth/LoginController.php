<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\UserDevice;
use App\Services\ActivityLogger;
use App\Services\SMSService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /**
     * Dynamically determine where to redirect users after login based on RBAC.
     */
    public function redirectTo()
    {
        // If the user is a Packer or Driver (lacks financial view), send them straight to invoices
        if (!Auth::user()->can('view financials')) {
            return '/invoices';
        }

        // Otherwise, send Admins and Managers to the main dashboard
        return '/dashboard';
    }

    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showLoginForm()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Redirect them to their role-specific dashboard
            return redirect($this->redirectTo());
        }

        // If not authenticated, show the login form
        return view('auth.login');
    }

    public function validateLogin(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ]);

        // Verify reCAPTCHA
        $recaptcha = new \ReCaptcha\ReCaptcha(env('GOOGLE_RECAPTCHA_SECRET'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

        if (!$response->isSuccess()) {
            return back()->withErrors(['captcha' => 'Google reCAPTCHA verification failed.']);
        }

        // Process the login
        // if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        //     return back()->with('error', 'Wrong username or password');
        // }

    }

    protected function authenticated(Request $request, $user)
    {

        $branch = Branch::find($user->branch_id);
        $user_phone = $user->phone;
        $user_email = $user->email;
        $masked_phone = maskPhoneNumber($user_phone);

        // Reset OTP verification status
        $user->otp_verified_at = null;

        // Get device information
        $agent = new Agent();
        $device = $agent->device();    // Gets device name (e.g., "iPhone", "Macintosh")
        $platform = $agent->platform();  // Gets platform name (e.g., "iOS", "Windows")
        $browser = $agent->browser();   // Gets browser name (e.g., "Chrome", "Safari")

        // Device details
        $deviceName = $agent->isMobile() ? $device : ($agent->isDesktop() ? $platform : 'Unknown Device');
        $deviceAgent = $browser;
        $deviceIp = $request->ip();

        // Get device location from ipinfo.io
        $locationResponse = Http::get("http://ipinfo.io/{$deviceIp}/json");

        if ($locationResponse->successful()) {
            $locationData = $locationResponse->json();
            if ($locationData['ip'] != '127.0.0.1') {
                $deviceLocation = $locationData['city'] . ', ' . $locationData['region'] . ', ' . $locationData['country'];
            } else {
                $deviceLocation = 'Location not available';
            }
        } else {
            $deviceLocation = 'Location not available';
        }

        // Find the device by User, IP, and Agent. 
        // If it exists, update the timestamp/location. If not, create a new one.
        UserDevice::updateOrCreate(
            [
                // The unique identifiers to search for
                'user_id'      => $user->id,
                'device_ip'    => $deviceIp,
                'device_agent' => $deviceAgent,
            ],
            [
                // The data to update (or insert if creating new)
                'device_name'     => $deviceName,
                'device_location' => $deviceLocation,
                'last_used_at'    => now(),
            ]
        );

        $branch->last_seen = now();
        $branch->last_ip = $deviceIp;
        $user->last_seen = now();
        $branch->save();
        $user->save();

        // Log a user login action
        ActivityLogger::log('User Login', $user->name . ' Logged in Successfully');

        $settings = SettingsHelper::getSettings();

        // Log::info('the settings are ' . $settings->enable_email_login);

        if (isset($settings->enable_email_login) && $settings->enable_email_login) {
            $currentDate = Carbon::now();
            $formattedCurrentDate = $currentDate->format('D, M d, Y, g:i A');

            $mergeData = [
                "date" => $formattedCurrentDate,
                "device_ip" => $deviceIp,
                "device_name" => $deviceName,
                "device_agent" => $deviceAgent,
                "device_location" => $deviceLocation,
            ];

            // Call the helper function
            $response = sendZeptoMail(
                $user->email,
                '2d6f.10b4e7b927f81eea.k1.aeca0a51-7a7a-11ef-9128-525400cbcb5e.1922443df73',
                $mergeData
            );

            // Log::info('the response is ' . json_encode($response));
            // return $response;
        }

        if ($settings->otp_method && $settings->otp_method != 'none' && !$user->otp_verified_at) {

            if ($settings->otp_method == 'authenticator_app') {
                $request->session()->put('twofa:user:id', $user->id);
                $request->session()->put('showQr', false);
                return redirect()->route('otp.auth.verify');
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $otp_message = '';
            $user->save();

            ActivityLogger::log('Login OTP', 'Generated Login OTP for ' . $user->name);

            if ($settings->otp_method === 'sms_otp') {

                $this->smsService->sendSMS($user->phone, "$otp is your verification code. For your security, do not share this code.");
                $otp_message = "Please enter the 6 digit code sent to <span class='text-primary fw-bold'>$masked_phone</span>";
            } elseif ($settings->otp_method === 'email_otp') {

                // Send OTP via Email
                $masked_email = maskEmail($user_email);
                $otp_message = "Please enter the 6 digit code sent to <span class='text-primary fw-bold'>$masked_email</span>";
                $mergeData = [
                    "title" => 'One Time Password',
                    "OTP" => $otp,
                    "name" => $user->name,
                    "company" => \App\Support\Brand::name(),
                    "contacts" => "+254741226412",
                ];

                // Call the helper function
                $response = sendZeptoMail(
                    $user->email,
                    '2d6f.10b4e7b927f81eea.k1.5a1be5d0-6f96-11ef-8b0b-525400fa05f6.191dce244ad',
                    $mergeData
                );
            } elseif ($settings->otp_method === 'whatsapp_otp') {

                // Send OTP via WhatsApp
                $otp_message = "Please enter the 6 digit code sent to your WhatsApp <span class='text-primary fw-bold'>$masked_phone</span>";
                sendWAOtpMessage('v18.0', formatPhoneNumber($user_phone), $otp, 'en');
            } else {
                return redirect()->back()->with('fail', 'Something went wrong');
            }

            return redirect()->route('otp.verify')->with('message', $otp_message);
        }
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        ActivityLogger::log('User Logout', $user->name . ' Logged out Successfully');
        if ($user) {
            $user->otp_verified_at = null;
            $user->save();
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // return $this->loggedOut($request) ?: view('auth.auth-logout');
        return view('auth.auth-logout');
    }
}
