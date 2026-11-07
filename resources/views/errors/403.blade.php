@extends('layouts.master-without-nav')

@section('title')
Forbidden
@endsection

@section('body')
<body>
@endsection
@section('content')
        <div class="auth-page-wrapper pt-5">
            <!-- auth page bg -->
            <div class="auth-one-bg-position auth-one-bg"  id="auth-particles">
                <div class="bg-overlay"></div>

                <div class="shape">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                        <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                    </svg>
                </div>
            </div>

            <!-- auth page content -->
            <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center pt-4">
                                <div class="">
                                    <img src="{{ asset('build/images/error.svg') }}" alt="" class="error-basic-img move-animation">
                                </div>
                                <div class="mt-n4">
                                    <h1 class="display-1 fw-medium">403</h1>
                                    <h3 class="text-uppercase">Forbidden 😭</h3>
                                    <p class="text-muted mb-4">
                                        {{ $exception->getMessage() ?: 'You do not have permission to access this resource. If you believe this is an error, please contact the site administrator!' }}
                                    </p>
                                    <a href="{{ route('root') }}" class="btn btn-success"><i class="ri-arrow-left-line me-1"></i>Dashboard</a>
                                    <a href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ri-arrow-left-line me-1"></i>@lang('translation.logout')</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                </div>
                <!-- end container -->
            </div>
            <!-- end auth page content -->

            <!-- footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> {{ \App\Support\Brand::name() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>
        <!-- end auth-page-wrapper -->

@endsection
@section('script')
<!-- particles js -->
<script src="{{ asset('build/libs/particles.js/particles.js') }}"></script>
<!-- particles app js -->
<script src="{{ asset('build/js/pages/particles.app.js') }}"></script>
@endsection
