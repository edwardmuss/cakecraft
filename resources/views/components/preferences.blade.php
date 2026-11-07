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
                           @if(!isset($branchSettings['otp_method']) || $branchSettings['otp_method'] === 'none') checked @endif>
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
                    <input class="form-check-input" type="checkbox" role="switch" id="sms_otp_switch"
                           name="settings[otp_method]" value="sms_otp"
                           @if(isset($branchSettings['otp_method']) && $branchSettings['otp_method'] === 'sms_otp') checked @endif>
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
                           @if(isset($branchSettings['otp_method']) && $branchSettings['otp_method'] === 'email_otp') checked @endif>
                </div>
            </div>
        </li>

        <!-- Authenticator App -->
        <li class="d-flex mt-2">
            <div class="flex-grow-1">
                <label for="authenticator_app_switch" class="form-check-label fs-14">Enable Two-factor Authenticator App</label>
                <p class="text-muted">Use an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator to generate the OTP.</p>
            </div>
            <div class="flex-shrink-0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="authenticator_app_switch"
                           name="settings[otp_method]" value="authenticator_app"
                           @if(isset($branchSettings['otp_method']) && $branchSettings['otp_method'] === 'authenticator_app') checked @endif>
                </div>
            </div>
        </li>

        <!-- Enable Email App -->
        <li class="d-flex mt-2">
            <div class="flex-grow-1">
                <label for="auth_app_switch" class="form-check-label fs-14">Enable Login Email Alerts</label>
                <p class="text-muted">Be notified whenever someone successfully logs into your account.</p>
            </div>
            <div class="flex-shrink-0">
                <div class="form-check form-switch">
                    <input type="hidden" name="settings[enable_email_login]" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="email_login_switch"
                           name="settings[enable_email_login]" value="1"
                           @if(isset($branchSettings['enable_email_login']) && $branchSettings['enable_email_login'] === '1') checked @endif>
                </div>
            </div>
        </li>
    </ul>
</div>

<script>
    
    // Handle checkbox state and set hidden input for the OTP method
    document.getElementById('otp_enabled_switch').addEventListener('change', function() {
        document.getElementById('otp_enabled').value = this.checked ? '1' : '0';
    });

    // Function to allow only one OTP method to be selected at a time, excluding the 'Enable Login Email Alerts' switch
    const otpMethodCheckboxes = document.querySelectorAll('.form-check-input:not(#email_login_switch)');

    // This event listener ensures only one OTP method can be selected at a time
    otpMethodCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Deselect all other OTP method checkboxes when one is selected
                otpMethodCheckboxes.forEach((otherCheckbox) => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });

    // 'Enable Login Email Alerts' is independent and not part of the OTP method group
    const emailLoginSwitch = document.getElementById('email_login_switch');
    emailLoginSwitch.addEventListener('change', function() {
        document.getElementById('email_login_switch').value = this.checked ? '1' : '0';
    });

</script>