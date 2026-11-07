@extends('layouts.master')
@section('title') Account Settings @endsection
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('title') Account Settings @endslot
@endcomponent

<div class="row gy-2 mb-2">
    @include('includes.flash_message')
</div>
<div class="row gy-2 mb-2">
    <!-- Hidden alert-error div -->
    <div class="alert alert-danger mt-3 d-none" id="errorAlert" role="alert">
        <!-- Error message will be displayed here -->
    </div>
</div>

<div class="row gy-2 mb-2">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <h5 class="card-title text-decoration-underline mb-3">Two-factor Authentication:</h5>
            <p class="text-muted">Please select your preferred OTP 2FA method</p>
            <ul class="list-unstyled mb-0">
                <!-- DISSABLE OTP -->
                <li class="d-flex">
                    <div class="flex-grow-1">
                        <label for="otp_enabled_switch" class="form-check-label fs-14">Disable OTP</label>
                        <p class="text-muted">This will disable all 2FA authentication methods</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="otp_enabled_switch"
                                   name="settings[otp_method]" value="none"
                                   @if(!isset($accountSettings['otp_method']) || $accountSettings['otp_method'] === 'none') checked @endif>
                        </div>
                    </div>
                </li>

                <!-- SMS OTP -->
                <li class="d-flex mt-2">
                    <div class="flex-grow-1">
                        <label for="otp_enabled_switch" class="form-check-label fs-14">Enable SMS OTP</label>
                        <p class="text-muted">Receive One-Time Password (OTP) via registered company phone number</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="otp_enabled_switch"
                                   name="settings[otp_method]" value="sms_otp"
                                   @if(isset($accountSettings['otp_method']) && $accountSettings['otp_method'] === 'sms_otp') checked @endif>
                        </div>
                    </div>
                </li>

                <!-- Email OTP -->
                <li class="d-flex mt-2">
                    <div class="flex-grow-1">
                        <label for="email_otp_switch" class="form-check-label fs-14">Enable Email OTP</label>
                        <p class="text-muted">Receive One-Time Password (OTP) via registered company email</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="email_otp_switch"
                                   name="settings[otp_method]" value="email_otp"
                                   @if(isset($accountSettings['otp_method']) && $accountSettings['otp_method'] === 'email_otp') checked @endif>
                        </div>
                    </div>
                </li>

                <!-- WhatsApp OTP -->
                <li class="d-flex mt-2">
                    <div class="flex-grow-1">
                        <label for="whatsapp_otp_switch" class="form-check-label fs-14">Enable WhatsApp OTP</label>
                        <p class="text-muted">Receive One-Time Password (OTP) via registered company phone number through WhatsApp</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="whatsapp_otp_switch"
                                   name="settings[otp_method]" value="whatsapp_otp"
                                   @if(isset($accountSettings['otp_method']) && $accountSettings['otp_method'] === 'whatsapp_otp') checked @endif>
                        </div>
                    </div>
                </li>

                <!-- Authenticator App -->
                <li class="d-flex mt-2">
                    <div class="flex-grow-1">
                        <label for="auth_app_switch" class="form-check-label fs-14">Enable Two-factor Auth App</label>
                        <p class="text-muted">Use an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator to generate the OTP.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="auth_app_switch"
                                   name="settings[otp_method]" value="authenticator_app"
                                   @if(isset($accountSettings['otp_method']) && $accountSettings['otp_method'] === 'authenticator_app') checked @endif>
                        </div>
                    </div>
                </li>
            </ul>
        </div>        
        <div>
            <h5 class="card-title text-decoration-underline mb-3">Low credit notification</h5>
            <p class="text-muted">Please indicate amount units at which you will be notified <strong>(0 to dissable)</strong> :
            </p>
            <div>
                <input type="number" class="form-control" id="low_credit_notification" name="settings[low_credit_notification]" placeholder="Number of units" value="{{ isset($accountSettings['low_credit_notification']) ? $accountSettings['low_credit_notification'] : 0 }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
    </form>
</div>

@endsection

@section('script')
<!-- App js -->
<script src="{{asset('build/js/app.js')}}"></script>

<script>
    // JavaScript to handle checkbox state and set hidden input accordingly
    document.getElementById('otp_enabled_switch').addEventListener('change', function() {
        document.getElementById('otp_enabled').value = this.checked ? '1' : '0';
    });

    // Function to allow only one checkbox to be selected
    const checkboxes = document.querySelectorAll('.form-check-input');
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Deselect all other checkboxes when one is selected
                checkboxes.forEach((otherCheckbox) => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });
</script>

@endsection
