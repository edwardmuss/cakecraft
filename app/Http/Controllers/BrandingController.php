<?php

namespace App\Http\Controllers;

use App\Services\OccasionService;
use App\Support\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function edit()
    {
        abort_if(!Auth::user()->can('update settings'), 403);
        $values = [];
        foreach (array_keys(Brand::FIELDS) as $key) {
            $values[$key] = Brand::get($key, '');
        }
        foreach (array_keys(Brand::COPY_DEFAULTS) as $key) {
            $values[$key] = Brand::copy($key);
        }
        $values['primary_color'] = Brand::color();
        $logo = Brand::logoUrl();

        return view('branding.edit', compact('values', 'logo'));
    }

    public function update(Request $request)
    {
        abort_if(!Auth::user()->can('update settings'), 403);

        $data = $request->validate([
            'business_name' => 'required|string|max:120',
            'tagline' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:40',
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{9,15}$/'],
            'email' => 'nullable|email|max:120',
            'address' => 'nullable|string|max:200',
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_headline' => 'nullable|string|max:120',
            'hero_text' => 'nullable|string|max:300',
            'step_1' => 'nullable|string|max:60',
            'step_2' => 'nullable|string|max:60',
            'step_3' => 'nullable|string|max:60',
            'step_4' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:200',
        ], ['whatsapp.regex' => 'WhatsApp must be digits only in international format, e.g. 254712345678.']);

        $branch = Auth::user()->branch_id;
        foreach (array_keys(Brand::FIELDS) as $key) {
            OccasionService::saveSetting($branch, $key, $data[$key] ?? '');
        }

        foreach (array_keys(Brand::COPY_DEFAULTS) as $key) {
            OccasionService::saveSetting($branch, $key, trim((string) ($data[$key] ?? '')));
        }

        if ($request->boolean('remove_logo')) {
            $old = OccasionService::setting($branch, 'brand_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            OccasionService::saveSetting($branch, 'brand_logo', '');
        }
        if ($request->hasFile('logo')) {
            $old = OccasionService::setting($branch, 'brand_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            OccasionService::saveSetting($branch, 'brand_logo', $request->file('logo')->store('branding', 'public'));
        }

        Brand::forget();

        return back()->with('success', 'Branding saved.');
    }
}
