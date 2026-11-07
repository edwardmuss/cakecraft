@extends('layouts.master-without-nav')

@section('title')
Internal Server Error
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
                            <h1 class="title text-muted">500</h1>
                        </div>
                        <div>
                            <h4>Internal Server Error!</h4>
                            <p class="text-muted w-75 mx-auto">An unexpected error occurred on the server. Please try again later. If the problem persists, contact the site administrator.</p>
                            <a href="{{ route('root') }}" class="btn btn-success"><i class="ri-arrow-left-line me-1"></i>Back to Dashboard</a>
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
