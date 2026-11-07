@extends('layouts.master-without-nav')
@section('title', 'Verify Account')

@section('content')
<style>
    .form-control-lg {
        min-height: calc(1.5em + 1.4rem + calc(var(--vz-border-width)* 2));
        padding: .7rem 1.0rem;
        font-size: 1.09375rem;
        border-radius: var(--vz-border-radius-lg);
    }
</style>
{{-- Note: Ensure your layout has the csrf meta tag, or we fetch it from the form --}}

<div class="auth-page-wrapper pt-5">
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>

    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 mt-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                        <i class="ri-mail-check-line"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 mt-4">
                                <div class="text-muted text-center mb-4 mx-lg-3">
                                    <h4>Verify Your Account</h4>
                                    <p>Please enter the 6-digit code sent to <br><span class="text-primary fw-bold">{{ $email }}</span>. If you haven't received the code with 2 minutes click on resend button to send it again.</p>
                                </div>

                                <div id="alert-placeholder">
                                    @if (session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                </div>

                                <form autocomplete="off" id="otp-form" method="POST" action="{{ route('verify.otp.submit') }}">
                                    @csrf
                                    <input type="hidden" name="email" id="user-email" value="{{ $email }}">

                                    <div class="row">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <div class="col-2">
                                                <div class="mb-3">
                                                    <label for="digit{{ $i }}-input" class="visually-hidden">Digit {{ $i }}</label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-light text-center"
                                                        onkeyup="moveToNext({{ $i }}, event)" maxLength="1"
                                                        id="digit{{ $i }}-input" name="digit{{ $i }}" required>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </form>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-success w-100" onclick="submitOtpForm();">Activate Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="mb-0">Didn't receive a code? <button id="resend-otp-btn" class="btn btn-link fw-semibold text-primary text-decoration-underline" onclick="resendOtp()" disabled>Resend</button> <span id="timer"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function getInputElement(index) {
        return document.getElementById("digit" + index + "-input");
    }

    function moveToNext(index, event) {
        const eventCode = event.which || event.keyCode;
        const inputElement = getInputElement(index);

        if (inputElement.value.length === 1) {
            if (index < 6) {
                getInputElement(index + 1).focus();
            } else {
                inputElement.blur();
                submitOtpForm();
            }
        }

        if (eventCode === 8 && index > 1) {
            getInputElement(index - 1).focus();
        }
    }

    function submitOtpForm() {
        const form = document.getElementById("otp-form");
        const formData = new FormData(form);
        
        // Fallback for CSRF token if meta tag is missing
        const csrfToken = document.querySelector('input[name="_token"]').value;

        const otp = Array.from({length: 6}, (_, i) => formData.get(`digit${i + 1}`)).join('');
        formData.append('otp', otp);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        }).then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.message || 'Unknown error'); });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                window.location.href = data.redirectUrl;
            } else {
                showErrorAlert(data.message);
                // Clear inputs on failure
                document.querySelectorAll("input[type='text']").forEach(input => input.value = '');
                getInputElement(1).focus();
            }
        }).catch(error => {
            showErrorAlert(error.message);
        });
    }

    function showErrorAlert(message) {
        const alertPlaceholder = document.getElementById('alert-placeholder');
        alertPlaceholder.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    function showSuccessAlert(message) {
        const alertPlaceholder = document.getElementById('alert-placeholder');
        alertPlaceholder.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    function resendOtp() {
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const email = document.getElementById('user-email').value;

        showSuccessAlert('Sending new code...');
        document.getElementById('resend-otp-btn').disabled = true;

        fetch('{{ route('verify.otp.resend') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email }) // Pass email to backend
        }).then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.message || 'Unknown error'); });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                localStorage.setItem('regOtpTimestamp', Date.now());
                startTimer();
                showSuccessAlert('New code sent successfully.');
            } else {
                showErrorAlert(data.message);
                document.getElementById('resend-otp-btn').disabled = false;
            }
        }).catch(error => {
            showErrorAlert(error.message);
            document.getElementById('resend-otp-btn').disabled = false;
        });
    }

    function startTimer() {
        const resendBtn = document.getElementById('resend-otp-btn');
        const timerSpan = document.getElementById('timer');
        const otpTimestamp = parseInt(localStorage.getItem('regOtpTimestamp'), 10) || 0;
        const now = Date.now();
        const remainingTime = 3 * 60 * 1000 - (now - otpTimestamp);

        if (remainingTime > 0) {
            resendBtn.disabled = true;
            const endTime = new Date(now + remainingTime);

            const interval = setInterval(() => {
                const now = new Date().getTime();
                const distance = endTime - now;

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerSpan.innerHTML = `in ${minutes}m ${seconds}s`;

                if (distance < 0) {
                    clearInterval(interval);
                    resendBtn.disabled = false;
                    timerSpan.innerHTML = '';
                }
            }, 1000);
        } else {
            resendBtn.disabled = false;
            timerSpan.innerHTML = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
        
        const inputs = document.querySelectorAll("input[type='text']");
        inputs[0].focus();

        inputs.forEach((input, index) => {
            input.addEventListener("paste", (event) => {
                event.preventDefault();
                let pasteData = (event.clipboardData || window.clipboardData).getData("text");

                if (/^\d{6}$/.test(pasteData)) {
                    pasteData.split("").forEach((char, idx) => {
                        if (inputs[idx]) inputs[idx].value = char;
                    });
                    inputs[5].focus();
                    submitOtpForm(); // Auto submit on paste
                }
            });
        });
    });
</script>
@endsection