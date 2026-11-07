@extends('layouts.master')
@section('title') Branding @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Branding</h4><div class="sub">Your business name, logo, colour and contact details. These appear on the website, the login page, the admin and SMS messages.</div></div>
</div>

<form method="POST" action="{{ route('branding.update') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card"><div class="card-body">
                <div class="cc-section-title">Business</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Business name *</label>
                        <input name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $values['business_name']) }}" required>@error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Tagline</label>
                        <input name="tagline" class="form-control" value="{{ old('tagline', $values['tagline']) }}" placeholder="Handcrafted cakes for every celebration"></div>
                    <div class="col-md-4"><label class="form-label">Phone</label>
                        <input name="phone" class="form-control" value="{{ old('phone', $values['phone']) }}" placeholder="+254 700 000 000"></div>
                    <div class="col-md-4"><label class="form-label">WhatsApp number</label>
                        <input name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp', $values['whatsapp']) }}" placeholder="254700000000">@error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-4"><label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $values['email']) }}"></div>
                    <div class="col-12"><label class="form-label">Address</label>
                        <input name="address" class="form-control" value="{{ old('address', $values['address']) }}" placeholder="Nairobi, Kenya"></div>
                </div>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="cc-section-title">Website wording</div>
                <p class="text-muted small">The text on your public home page. Clear a box to go back to the default wording.</p>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Home page headline</label>
                        <input name="hero_headline" class="form-control" maxlength="120" value="{{ old('hero_headline', $values['hero_headline']) }}"></div>
                    <div class="col-12"><label class="form-label">Home page intro</label>
                        <textarea name="hero_text" rows="2" class="form-control" maxlength="300">{{ old('hero_text', $values['hero_text']) }}</textarea></div>
                    @foreach([1 => 'Step 1', 2 => 'Step 2', 3 => 'Step 3', 4 => 'Step 4'] as $n => $label)
                    <div class="col-md-6"><label class="form-label">{{ $label }} (how ordering works)</label>
                        <input name="step_{{ $n }}" class="form-control" maxlength="60" value="{{ old('step_'.$n, $values['step_'.$n]) }}"></div>
                    @endforeach
                    <div class="col-12"><label class="form-label">Search engine description</label>
                        <textarea name="meta_description" rows="2" class="form-control" maxlength="200">{{ old('meta_description', $values['meta_description']) }}</textarea>
                        <div class="form-text">Shown under your site name in Google results.</div></div>
                </div>
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card"><div class="card-body">
                <div class="cc-section-title">Logo &amp; colour</div>
                <div class="text-center mb-3"><img src="{{ $logo }}" alt="Logo" style="max-height:90px;max-width:100%;border-radius:8px"></div>
                <label class="form-label">Upload new logo</label>
                <input type="file" name="logo" accept="image/*" class="form-control mb-1 @error('logo') is-invalid @enderror">@error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div class="form-text mb-2">JPG, PNG or WebP, up to 2 MB. A square image works best.</div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="rm"><label class="form-check-label" for="rm">Remove uploaded logo (use default)</label></div>
                <label class="form-label">Main colour</label>
                <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $values['primary_color']) }}">
                <div class="form-text">Used for buttons and highlights across the admin and the website.</div>
            </div></div>
            <button class="btn btn-primary w-100">Save branding</button>
        </div>
    </div>
</form>
@endsection
