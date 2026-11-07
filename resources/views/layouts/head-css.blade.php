@yield('css')
<!-- Layout config Js -->
<script src="{{ asset('build/js/layout.js') }}"></script>
<!-- Bootstrap Css -->
<link href="{{ asset('build/css/bootstrap.min.css') }}"  rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ asset('build/css/app.min.css') }}"  rel="stylesheet" type="text/css" />
<!-- custom Css-->
<link rel="stylesheet" href="{{ asset('build/css/custom.min.css') }}?v={{ file_exists(public_path('build/css/custom.min.css')) ? filemtime(public_path('build/css/custom.min.css')) : time() }}">

<!-- Cakecraft admin skin -->
<link rel="stylesheet" href="{{ asset('build/css/cakecraft-admin.css') }}?v={{ file_exists(public_path('build/css/cakecraft-admin.css')) ? filemtime(public_path('build/css/cakecraft-admin.css')) : time() }}">
@include('includes.brand-css')
