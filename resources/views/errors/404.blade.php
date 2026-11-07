@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.404-cover')
@endsection

@section('body')

    <body>
    @endsection
    @section('content')
        <!-- auth-page wrapper -->
        <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

            <!-- auth-page content -->
            <div class="auth-page-content overflow-hidden p-0">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-8">
                            <div class="text-center">
                                <img src="{{ asset('build/images/error400-cover.png') }}" alt="error img" class="img-fluid">
                                <div class="mt-3">
                                    <h3 class="text-uppercase">Sorry, Page not Found 😭</h3>
                                    <p class="text-muted mb-4">The page you are looking for could not be found. It may have been removed or you may have mistyped the address. Please check the URL or return to the homepage!</p>
                                    
                                    <a href="{{ route('root') }}" class="btn btn-primary"><i class="ri-arrow-left-line me-1"></i>Go to
                                        Dashboard</a>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end auth-page content -->
        </div>
        <!-- end auth-page-wrapper -->
    @endsection
