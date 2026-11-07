@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signup')
@endsection
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@20.0.5/build/css/intlTelInput.css">
    <style>
        /* Force the phone input to take full width of the bootstrap column */
        .iti { width: 100%; }
    </style>
@endsection
@section('content')

    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="index" class="d-inline-block auth-logo">
                                    <img src="{{ asset('build/images/logo-light.png') }}" alt="" height="20">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">Communication Made Easier</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-9 col-lg-9 col-xl-9">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Create New Account</h5>
                                    <p class="text-muted">Get your free Bulk SMS account now</p>
                                </div>
                                <div class="p-2 mt-4">
                                    
                                    {{-- Flash message for any global errors --}}
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif

                                    <form class="needs-validation" novalidate method="POST" action="{{ route('register') }}">
                                        @csrf

                                        {{-- SECTION: COMPANY DETAILS --}}
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Company Details</h6>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="branch_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('branch_name') is-invalid @enderror"
                                                    name="branch_name" value="{{ old('branch_name') }}" id="branch_name"
                                                    placeholder="Enter company name" required>
                                                @error('branch_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter company name</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="company_phone_visible" class="form-label">Company Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror" id="company_phone_visible" placeholder="e.g., 700 000 000" required>
                                                
                                                <input type="hidden" name="company_phone" id="company_phone_hidden" value="{{ old('company_phone') }}">
                                                
                                                @error('company_phone')
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter company phone</div>
                                            </div>

                                            {{-- <div class="col-md-6 mb-3">
                                                <label for="company_phone" class="form-label">Company Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror"
                                                    name="company_phone" value="{{ old('company_phone') }}" id="company_phone"
                                                    placeholder="e.g., +254700000000" required>
                                                @error('company_phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter company phone</div>
                                            </div> --}}
                                        </div>

                                        {{-- SECTION: PERSONAL DETAILS --}}
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3 mt-4 border-top pt-3">Personal Details</h6>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name') }}" id="name"
                                                    placeholder="Enter full name" required>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter full name</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="useremail" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email') }}" id="useremail"
                                                    placeholder="Enter email address" required>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter a valid email</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="personal_phone_visible" class="form-label">Personal Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="personal_phone_visible" placeholder="e.g., 711 111 111" required>
                                                
                                                <input type="hidden" name="phone" id="personal_phone_hidden" value="{{ old('phone') }}">
                                                
                                                @error('phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter personal phone</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="userpassword" class="form-label">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                    name="password" id="userpassword" placeholder="Enter password" required>                                                
                                                
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter password</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="input-password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    name="password_confirmation" id="input-password" placeholder="Confirm Password" required>
                                                
                                                {{-- Match Error UI --}}
                                                <div id="password-match-error" class="text-danger fs-12 mt-1 fw-medium" style="display: none;">
                                                    <i class="ri-error-warning-line me-1"></i> Passwords do not match
                                                </div>
                                            </div>
                                            {{-- Dynamic Password Strength UI --}}
                                            <div id="password-contain" class="p-3 bg-light mb-2 rounded mt-2" style="display: none;">
                                                <h5 class="fs-13">Password must contain:</h5>
                                                <div id="pass-length" class="text-danger fs-12 mb-1"><i class="ri-close-circle-line me-1"></i> Minimum <b>8 characters</b></div>
                                                <div id="pass-lower" class="text-danger fs-12 mb-1"><i class="ri-close-circle-line me-1"></i> At least one <b>lowercase</b> letter (a-z)</div>
                                                <div id="pass-upper" class="text-danger fs-12 mb-1"><i class="ri-close-circle-line me-1"></i> At least one <b>uppercase</b> letter (A-Z)</div>
                                                <div id="pass-number" class="text-danger fs-12 mb-1"><i class="ri-close-circle-line me-1"></i> At least one <b>number</b> (0-9)</div>
                                                <div id="pass-symbol" class="text-danger fs-12 mb-0"><i class="ri-close-circle-line me-1"></i> At least one <b>symbol</b> (@, #, $, etc.)</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <p class="mb-0 fs-12 text-muted fst-italic">By registering you agree to the
                                                RebueText <a href="{{ route('term-of-use') }}" target="_blank" class="text-primary text-decoration-underline fst-normal fw-medium">Terms of Use</a>
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-success w-100" type="submit">Sign Up</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title text-muted">Create account with</h5>
                                            </div>

                                            <div>
                                                <a href="{{ url('auth/facebook') }}"
                                                    class="btn btn-primary btn-icon waves-effect waves-light">
                                                    <i class="ri-facebook-fill fs-16"></i>
                                                </a>
                                                <a href="{{ url('auth/google') }}"
                                                    class="btn btn-danger btn-icon waves-effect waves-light">
                                                    <i class="ri-google-fill fs-16"></i>
                                                </a>
                                                <a href="{{ url('auth/github') }}"
                                                    class="btn btn-dark btn-icon waves-effect waves-light">
                                                    <i class="ri-github-fill fs-16"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            </div>
                        <div class="mt-4 text-center">
                            <p class="mb-0">Already have an account? <a href="{{ route('login') }}"
                                    class="fw-semibold text-primary text-decoration-underline"> Signin </a> </p>
                        </div>

                    </div>
                </div>
                </div>
            </div>
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script> document.write(new Date().getFullYear())</script> RebueText. Powered by <a href="https://cloudrebue.co.ke" target="_blank">Cloud Rebue Solutions</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        </div>
    @endsection

@section('script')
    <script src="{{ asset('build/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('build/js/pages/particles.app.js') }}"></script>
    <script src="{{ asset('build/js/pages/form-validation.init.js') }}"></script>
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
            document.querySelector("form.needs-validation").addEventListener("submit", function() {
                if (companyPhoneVisible.value.trim()) {
                    companyPhoneHidden.value = itiCompany.getNumber();
                }
                if (personalPhoneVisible.value.trim()) {
                    personalPhoneHidden.value = itiPersonal.getNumber();
                }
            });

            // Password Strength
            const passwordInput = document.getElementById("userpassword");
            const confirmInput = document.getElementById("input-password");
            const submitBtn = document.querySelector("button[type='submit']");
            
            const strengthBox = document.getElementById("password-contain");
            const matchError = document.getElementById("password-match-error");

            // Requirement Elements
            const reqLength = document.getElementById("pass-length");
            const reqLower = document.getElementById("pass-lower");
            const reqUpper = document.getElementById("pass-upper");
            const reqNumber = document.getElementById("pass-number");
            const reqSymbol = document.getElementById("pass-symbol");

            let isStrengthValid = false;
            let isMatchValid = false;

            // 1. Validate Password Strength
            function validatePassword() {
                const val = passwordInput.value;
                
                // Show the criteria box once they start typing
                strengthBox.style.display = val.length > 0 ? "block" : "none";

                // Regex Checks
                const checks = {
                    length: val.length >= 8,
                    lower: /[a-z]/.test(val),
                    upper: /[A-Z]/.test(val),
                    number: /[0-9]/.test(val),
                    symbol: /[^A-Za-z0-9]/.test(val) // Any character that isn't a letter or number
                };

                // Update UI Checkmarks
                updateRequirementUI(reqLength, checks.length);
                updateRequirementUI(reqLower, checks.lower);
                updateRequirementUI(reqUpper, checks.upper);
                updateRequirementUI(reqNumber, checks.number);
                updateRequirementUI(reqSymbol, checks.symbol);

                // Check if all rules are met
                isStrengthValid = Object.values(checks).every(Boolean);
                
                // Re-check match in case they changed the main password after typing confirmation
                checkMatch();
            }

            // Helper to toggle green check / red cross
            function updateRequirementUI(element, isValid) {
                if (isValid) {
                    element.classList.remove("text-danger");
                    element.classList.add("text-success");
                    element.innerHTML = element.innerHTML.replace('ri-close-circle-line', 'ri-checkbox-circle-fill');
                } else {
                    element.classList.remove("text-success");
                    element.classList.add("text-danger");
                    element.innerHTML = element.innerHTML.replace('ri-checkbox-circle-fill', 'ri-close-circle-line');
                }
            }

            // 2. Validate Password Match
            function checkMatch() {
                const passVal = passwordInput.value;
                const confirmVal = confirmInput.value;

                if (confirmVal.length > 0) {
                    if (passVal !== confirmVal) {
                        matchError.style.display = "block";
                        confirmInput.classList.add("is-invalid");
                        confirmInput.classList.remove("is-valid");
                        isMatchValid = false;
                    } else {
                        matchError.style.display = "none";
                        confirmInput.classList.remove("is-invalid");
                        confirmInput.classList.add("is-valid");
                        isMatchValid = true;
                    }
                } else {
                    matchError.style.display = "none";
                    confirmInput.classList.remove("is-invalid", "is-valid");
                    isMatchValid = false;
                }
                
                toggleSubmitButton();
            }

            // 3. Enable/Disable Submit Button
            function toggleSubmitButton() {
                // Button is only enabled if strength is perfect AND passwords match
                submitBtn.disabled = !(isStrengthValid && isMatchValid);
            }

            // Attach Event Listeners
            passwordInput.addEventListener("input", validatePassword);
            confirmInput.addEventListener("input", checkMatch);

            // Disable submit button on initial page load
            submitBtn.disabled = true;
        });
    </script>
@endsection