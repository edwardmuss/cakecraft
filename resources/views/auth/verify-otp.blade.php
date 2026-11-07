@extends('layouts.master-without-nav')
@section('title')
@lang('translation.two-step-verification')
@endsection
@section('content')
<style>
    .form-control-lg {
    min-height: calc(1.5em + 1.4rem + calc(var(--vz-border-width)* 2));
    padding: .7rem 1.0rem;
    font-size: 1.09375rem;
    border-radius: var(--vz-border-radius-lg);
}
</style>
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
<div class="auth-page-wrapper pt-5">
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>

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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="index" class="d-inline-block auth-logo">
                                    <img src="{{ asset('build/images/logo-light.png') }}" alt="" height="20">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">Communication Made Easy</p>
                        </div>
                    </div>
                </div>
    
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <div class="avatar-lg mx-auto">
                                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                            <i class="ri-mail-line"></i>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="p-2 mt-4">
                                    <div class="text-muted text-center mb-4 mx-lg-3">
                                        <h4 class="">Verify Login</h4>
                                        @if (session('message'))
                                            <p>{!! session('message') !!}</p>
                                        @endif
                                    </div>
    
                                    <div id="alert-placeholder"></div> <!-- Placeholder for alert -->
                                    <form autocomplete="off" id="otp-form" method="POST" action="{{ route('otp.verify') }}">
                                        @csrf
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
                                        <button type="button" class="btn btn-success w-100" onclick="submitOtpForm();">Confirm</button>
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
    
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <script> document.write(new Date().getFullYear())</script> Holy Communion Elements . Powered by <a href="https://cloudrebue.co.ke" target="_blank" rel="noopener noreferrer">Cloud Rebue Solutions</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

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
                console.error(data.message);
                showErrorAlert(data.message);
            }
        }).catch(error => {
            console.error('There was a problem with the fetch operation:', error);
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

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('otp-form').addEventListener('submit', function(event) {
            event.preventDefault();
            submitOtpForm();
        });
    });

    function resendOtp() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        showSuccessAlert('Please Wait...');
        fetch('{{ route('otp.resend') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.message || 'Unknown error'); });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                localStorage.setItem('otpTimestamp', Date.now());
                startTimer();
                showSuccessAlert('OTP has been resent successfully.');
            } else {
                showErrorAlert(data.message);
            }
        }).catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            showErrorAlert(error.message);
        });
    }

    function startTimer() {
        const resendBtn = document.getElementById('resend-otp-btn');
        const timerSpan = document.getElementById('timer');
        const otpTimestamp = parseInt(localStorage.getItem('otpTimestamp'), 10);
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

                timerSpan.innerHTML = `${minutes}m ${seconds}s`;

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
        document.getElementById('otp-form').addEventListener('submit', function(event) {
            event.preventDefault();
            submitOtpForm();
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll("input[type='text']");

        inputs.forEach((input, index) => {
            input.addEventListener("paste", (event) => {
                event.preventDefault();
                let pasteData = (event.clipboardData || window.clipboardData).getData("text");

                if (/^\d{6}$/.test(pasteData)) { // Ensure it's exactly 6 digits
                    pasteData.split("").forEach((char, idx) => {
                        if (inputs[idx]) {
                            inputs[idx].value = char;
                        }
                    });
                    inputs[5].focus(); // Move focus to the last input
                }
            });

            input.addEventListener("keyup", (event) => {
                let eventCode = event.which || event.keyCode;

                if (input.value.length === 1 && index < 5) {
                    inputs[index + 1].focus();
                } else if (eventCode === 8 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    });

</script>


@endsection
@section('script')
    <script src="{{ asset('build/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('build/js/pages/particles.app.js') }}"></script>
    {{-- <script src="{{ asset('build/js/pages/two-step-verification.init.js') }}"></script> --}}
@endsection
