@extends('layouts.master-without-nav')
@section('title', 'Complete Your Profile')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@20.0.5/build/css/intlTelInput.css">
    <style>
        /* Force the phone input to take full width of its container */
        .iti { width: 100%; }
    </style>
@endsection

@section('content')
<div class="auth-page-wrapper pt-5">
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
    </div>

    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8 col-xl-8 mt-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Almost there!</h5>
                                <p class="text-muted">You successfully authenticated as <strong>{{ $socialUser['email'] }}</strong>.<br> We just need a few details about your company.</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <form method="POST" action="{{ route('register.store_profile') }}" id="complete-profile-form">
                                    @csrf
                                    
                                    {{-- Company Name --}}
                                    <div class="mb-3">
                                        <label for="branch_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('branch_name') is-invalid @enderror" name="branch_name" value="{{ old('branch_name') }}" placeholder="Enter Company Name" required>
                                        @error('branch_name')
                                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    {{-- Company Phone (Intl) --}}
                                    <div class="mb-3">
                                        <label for="company_phone_visible" class="form-label">Company Phone (Bulk SMS Sender) <span class="text-danger">*</span></label>
                                        
                                        <input type="text" class="form-control @error('company_phone') is-invalid @enderror" id="company_phone_visible" placeholder="e.g., 700 000 000" required>
                                        
                                        <input type="hidden" name="company_phone" id="company_phone_hidden" value="{{ old('company_phone') }}">
                                        
                                        @error('company_phone')
                                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    {{-- User Personal Phone (Intl) --}}
                                    <div class="mb-4">
                                        <label for="personal_phone_visible" class="form-label">Your Personal Phone <span class="text-danger">*</span></label>
                                        
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="personal_phone_visible" placeholder="e.g., 711 111 111" required>
                                        
                                        <input type="hidden" name="phone" id="personal_phone_hidden" value="{{ old('phone') }}">
                                        
                                        @error('phone')
                                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-success w-100" type="submit">Complete Registration</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('build/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('build/js/pages/particles.app.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@20.0.5/build/js/intlTelInput.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Configuration for the phone inputs
            const itiConfig = {
                initialCountry: "ke", // Default to Kenya
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@20.0.5/build/js/utils.js",
            };

            // Initialize Company Phone
            const companyPhoneVisible = document.querySelector("#company_phone_visible");
            const companyPhoneHidden = document.querySelector("#company_phone_hidden");
            const itiCompany = window.intlTelInput(companyPhoneVisible, itiConfig);

            // Initialize Personal Phone
            const personalPhoneVisible = document.querySelector("#personal_phone_visible");
            const personalPhoneHidden = document.querySelector("#personal_phone_hidden");
            const itiPersonal = window.intlTelInput(personalPhoneVisible, itiConfig);

            // On Form Submit, populate the hidden inputs with the full international numbers
            document.querySelector("#complete-profile-form").addEventListener("submit", function(e) {
                if (companyPhoneVisible.value.trim()) {
                    companyPhoneHidden.value = itiCompany.getNumber();
                }
                if (personalPhoneVisible.value.trim()) {
                    personalPhoneHidden.value = itiPersonal.getNumber();
                }
            });
        });
    </script>
@endsection