<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * White-label branding. Each value comes from the Branding page (saved in the settings table);
 * anything not set there falls back to the CAKECRAFT_* values in .env / config/cakecraft.php.
 */
class Brand
{
    /** Keys editable on the Branding page, with the config key used as the fallback. */
    public const FIELDS = [
        'business_name' => 'business_name',
        'tagline'       => 'tagline',
        'phone'         => 'phone',
        'whatsapp'      => 'whatsapp',
        'email'         => 'email',
        'address'       => 'address',
        'primary_color' => 'primary_color',
    ];

    /** Website wording that clients can rewrite. Empty = use the default text. */
    public const COPY_DEFAULTS = [
        'hero_headline' => 'Cakes made for the moments you remember.',
        'hero_text'     => 'Pick a flavour, choose your size and tell us the date. We bake, decorate and have it ready for pickup or delivery.',
        'step_1'        => 'Pick your cake & size',
        'step_2'        => 'Send your request',
        'step_3'        => 'We confirm & you pay on M-Pesa',
        'step_4'        => 'Collect or get it delivered',
        'meta_description' => 'Custom birthday, wedding and celebration cakes. Pick a flavour and size, request your cake online.',
    ];

    private static array $cache = [];

    public static function branchId(): ?int
    {
        if (Auth::check() && Auth::user()->branch_id) {
            return (int) Auth::user()->branch_id;
        }
        $configured = config('cakecraft.branch_id');
        if ($configured) {
            return (int) $configured;
        }
        try {
            return Branch::query()->orderBy('id')->value('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function stored(): array
    {
        $branch = self::branchId();
        if (isset(self::$cache[$branch])) {
            return self::$cache[$branch];
        }
        $values = [];
        try {
            if ($branch && Schema::hasTable('settings')) {
                $values = Setting::where('branch_id', $branch)
                    ->whereIn('key', array_merge(array_keys(self::FIELDS), array_keys(self::COPY_DEFAULTS), ['brand_logo']))
                    ->pluck('value', 'key')->all();
            }
        } catch (\Throwable $e) {
            $values = [];
        }
        return self::$cache[$branch] = $values;
    }

    public static function get(string $key, $default = null)
    {
        $value = self::stored()[$key] ?? null;
        if ($value !== null && $value !== '') {
            return $value;
        }
        return config('cakecraft.' . (self::FIELDS[$key] ?? $key), $default);
    }

    public static function copy(string $key): string
    {
        $value = self::stored()[$key] ?? null;
        return ($value !== null && trim($value) !== '') ? $value : (self::COPY_DEFAULTS[$key] ?? '');
    }

    public static function name(): string
    {
        return (string) self::get('business_name', config('app.name'));
    }

    public static function logoUrl(): string
    {
        $path = self::stored()['brand_logo'] ?? null;
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        return asset(config('cakecraft.logo', 'images/cakecraft-logo.jpg'));
    }

    /** Absolute file path of the logo, for PDFs (DomPDF needs a local file). */
    public static function logoFile(): string
    {
        $path = self::stored()['brand_logo'] ?? null;
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }
        return public_path(config('cakecraft.logo', 'images/cakecraft-logo.jpg'));
    }

    public static function forget(): void
    {
        self::$cache = [];
    }

    public static function color(): string
    {
        $c = (string) self::get('primary_color', '#b76e79');
        return preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? strtolower($c) : '#b76e79';
    }

    private static function mix(string $hex, string $with, float $ratio): string
    {
        [$r1, $g1, $b1] = sscanf($hex, '#%02x%02x%02x');
        [$r2, $g2, $b2] = sscanf($with, '#%02x%02x%02x');
        $mix = fn ($a, $b) => (int) round($a + ($b - $a) * $ratio);
        return sprintf('#%02x%02x%02x', $mix($r1, $r2), $mix($g1, $g2), $mix($b1, $b2));
    }

    /** A <style> block that re-colours both the admin and the public site. */
    public static function css(): string
    {
        $c = self::color();
        $dark = self::mix($c, '#000000', 0.18);
        $soft = self::mix($c, '#ffffff', 0.9);
        [$r, $g, $b] = sscanf($c, '#%02x%02x%02x');

        return "<style>:root{--cc-accent:{$c};--cc-accent-dark:{$dark};--cc-accent-soft:{$soft};"
            . "--rose:{$c};--rose-dark:{$dark};--blush:{$soft};--cc-accent-rgb:{$r},{$g},{$b}}"
            . ".form-control:focus,.form-select:focus{box-shadow:0 0 0 .15rem rgba({$r},{$g},{$b},.18)!important}</style>";
    }
}
