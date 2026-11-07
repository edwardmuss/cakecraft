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
                            <p class="mt-3 fs-15 fw-medium">Built in Africa for Africa</p>
                        </div>
                    </div>
                </div>
    
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-8 col-xl-8">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <div class="avatar-lg mx-auto">
                                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                            <i class="ri-key-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-body">
                                    @include('includes.flash_message')

                                    @if(isset($showQr) && $showQr)
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                Scan the QR Code with your Authenticator App
                                            </div>
                                            <div class="card-body text-center">
                                                <img src="{{ $qrCode }}" alt="QR Code">
                                                <p class="mt-3 text-muted">Or enter this key manually:</p>
                                                <code class="h5">{{ $plainSecret }}</code>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="card mt-4">
                                        <div class="card-header">
                                            Verify Authentication Code
                                        </div>
                                        <div class="card-body">
                                            <div id="alert-placeholder"></div>

                                            <form autocomplete="off" id="otp-form" method="POST" action="{{ route('otp.verify') }}">
                                                @csrf
                                                <input type="hidden" name="2fa_auth" value="yes">

                                                <div class="form-group">
                                                    <label for="otp">One-Time Password</label>
                                                    <input type="number" name="otp" id="otp" 
                                                        class="form-control @error('otp') is-invalid @enderror" 
                                                        required 
                                                        placeholder="Enter the 6-digit code from your Authenticator App">
                                                    @error('otp')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Open your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.)  
                                                        and enter the 6-digit code.  
                                                        You can use either the QR-scanned entry or the manual secret key entry.
                                                    </small>
                                                </div>

                                                <div class="mt-3">
                                                    <button type="submit" class="btn btn-success w-100">Verify 2FA</button>
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
        </div>
    
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> Chanzo Technologies</p>
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
    {{-- <script src="{{ asset('build/js/pages/two-step-verification.init.js') }}"></script> --}}
@endsection
