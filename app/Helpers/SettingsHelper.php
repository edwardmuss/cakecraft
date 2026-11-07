<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingsHelper
{
    public static function getSettings()
    {
        if (Auth::check()) {
            $branchId = Auth::user()->branch_id;
            $settings = Setting::where('branch_id', $branchId)->pluck('value', 'key')->toArray();
            return (object) array_merge([
                'otp_method' => 'none', // Default value if not set
            ], $settings); // Merge with the actual settings
        } else {
            return (object) []; // Return an empty object or default settings if not authenticated
        }
    }

    public static function isOtpEnabled()
    {
        $settings = self::getSettings();
        return $settings->otp_enabled ?? 'none'; // Return none if otp_enabled is not set
    }
}
