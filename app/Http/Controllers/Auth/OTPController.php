<?php

namespace App\Http\Controllers\Auth;

use Google2FA;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use BaconQrCode\Writer;
use App\Services\SMSService;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class OTPController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    public function showAuthApp(Request $request)
    {
        $user = Auth::user();
        if (!$user->twofa_secret) {
            $plainSecret = Google2FA::generateSecretKey();
            $encrypted_secret = encrypt($plainSecret);
            $user->update([
                'twofa_secret' => $encrypted_secret,
                // 'twofa_qr' => $qrCode,
            ]);
        } else {
            // secret already exists → just decrypt it
            $plainSecret = decrypt($user->twofa_secret);
        }

        $qrCode = $this->generateQrCode(Google2FA::getQRCodeUrl(
            config('app.name'),
            $user->email ?? $user->phone,
            $plainSecret
        ));

        $showQr = $request->session()->get('showQr', true);
        $request->session()->put('twofa:user:id', $user->id);

        return view('auth.auth-app-screen', compact('showQr', 'qrCode', 'plainSecret'));
    }

    /**
     * Generate a QR Code image data URI using Bacon QR Code.
     *
     * @param string $qrCodeUrl
     * @return string
     */
    private function generateQrCode(string $qrCodeUrl): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrCodeUrl);

        // Convert SVG to Data URI
        $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

        return $dataUri;
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user();

        if ($request->has('2fa_auth')) {
            try {
                $twofa = $request->session()->get('twofa:user:id', true);

                if (!$twofa) {
                    return redirect()->route('login');
                    return response()->json([
                        'success' => true,
                        'redirectUrl' => url('/login')
                    ]);
                }

                $user = User::find($twofa);

                if (!$user || !$user->twofa_secret) {
                    return redirect()->back()->with('fail', 'Invalid authentication code..');
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid user or 2FA not set.'
                    ], 400);
                }

                if (Google2FA::verifyKey(decrypt($user->twofa_secret), $request->otp)) {
                    // return $request->all();
                    // Auth::login($user);
                    $request->session()->forget('twofa:user:id');

                    $user->branch->setSetting('otp_method', 'authenticator_app');

                    $user->otp_verified_at = now();
                    $user->otp = null;
                    $user->otp_expires_at = null;
                    $user->save();

                    return redirect()->route('root')->with('success', 'Two Factor Settings updated successfully.');

                    return response()->json([
                        'success' => true,
                        'redirectUrl' => route('root')
                    ]);
                } else {
                    // return $request->all();
                    return redirect()->back()->with('fail', 'Invalid authentication code..');

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid authentication code.'
                    ], 400);
                }
            } catch (\Throwable $e) {
                // Log for debugging
                Log::error('2FA verification failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                return redirect()->route('settings.index')->with('fail', '2FA verification failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => '2FA verification failed: ' . $e->getMessage()
                ], 500);
            }
        }


        if ($user->otp === $request->otp && $user->otp_expires_at->isFuture()) {
            // OTP is valid
            $user->otp_verified_at = now();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json(['success' => true, 'redirectUrl' => route('root')]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();

        // Reset OTP verification status
        $user->otp_verified_at = null;

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $settings = SettingsHelper::getSettings();
        $branch = Account::find($user->branch_id);
        $account_phone = $branch->company_phone;
        $account_email = $branch->company_email;
        $masked_phone = maskPhoneNumber($account_phone);

        if ($settings->otp_method === 'sms_otp') {
            $this->smsService->sendSMS($account_phone, "$otp is your verification code. For your security, do not share this code.");
        } elseif ($settings->otp_method === 'email_otp') {

            // Send OTP via Email
            $masked_email = maskEmail($account_email);
            $mergeData = [
                "title" => 'One Time Password',
                "OTP" => $otp,
                "name" => $user->name,
                "company" => \App\Support\Brand::name(),
                "contacts" => "+254741226412",
            ];

            // Call the helper function
            $response = sendZeptoMail(
                $account_email,
                '2d6f.10b4e7b927f81eea.k1.5a1be5d0-6f96-11ef-8b0b-525400fa05f6.191dce244ad',
                $mergeData
            );
        } elseif ($settings->otp_method === 'whatsapp_otp') {
            // Send OTP via WhatsApp
            sendWAOtpMessage('v18.0', formatPhoneNumber($account_phone), $otp, 'en');
        }

        return response()->json(['success' => true, 'message' => 'OTP has been resent.']);
    }
}
