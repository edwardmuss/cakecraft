@extends('layouts.master-without-nav')

@section('title')
Session Expired
@endsection

@section('body')
<body>
@endsection
@section('content')
       <!-- auth-page wrapper -->
       <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden p-0">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 text-center">
                        <div class="error-500 position-relative">
                            <img src="{{ asset('build/images/error500.png') }}" alt="" class="img-fluid error-500-img error-img" />
                            <h1 class="title text-muted">419</h1>
                        </div>
                        <div>
                            <h4>Session Expired</h4>
                            <p class="text-muted w-75 mx-auto">Your session has expired due to inactivity. Please refresh the page and try again. If the problem persists, log in again.</p>
                            <a href="{{ route('login') }}" class="btn btn-success"><i class="ri-login-circle-line me-1"></i>Login Again</a>
                        </div>
                    </div><!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth-page content -->
    </div>
    <!-- end auth-page-wrapper -->
@endsection
