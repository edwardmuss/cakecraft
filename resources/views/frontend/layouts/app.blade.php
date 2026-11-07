<!DOCTYPE html>
<html class="no-js" lang="zxx" dir="ltr">

<head>
    <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="author" content="Edward Muss">
   <meta name="description" content="@yield('description')">
   <meta name="keywords" content="@yield('keywords')">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="canonical" href="{{ url()->current() }}">

   <!-- TWITTER CARD -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:site" content="@rebuetext" />
   <meta name="twitter:title" content="@yield('title')" />
   <meta name="twitter:description" content="@yield('description')" />
   <meta name="twitter:image" content="@yield('image')" />
   <!-- END TWITTER CARD -->

   <!--START og for social sharing -->
   <meta property="og:site_name" content="RebueText | Rebue Text Bulk SMS, WhatsApp Messaging, Shortcodes & USSD Solutions">
   <meta property="og:title" content="@yield('title') - {{ env('APP_NAME') }}">
   <meta property="og:description" content="@yield('description')">
   <meta property="og:image" itemprop="image"
      content="@yield('image')">
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <!--END og for social sharing -->

   <!-- SITE TITLE -->
   <title>
      @yield('title') - {{ env('APP_NAME') }}
   </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('build/images/favicon.ico') }}">

    <!-- CSS
        ============================================ -->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/vendor/bootstrap.min.css') }}">

    <!-- Font Family CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/vendor/fontawesome-all.min.css') }}">

    <!-- Swiper slider CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/plugins/swiper.min.css') }}">

    <!-- animate-text CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/plugins/animate-text.css') }}">

    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/plugins/animate.min.css') }}">

    <!-- Light gallery CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/plugins/lightgallery.min.css') }}">

    <!-- Vendor & Plugins CSS (Please remove the comment from below vendor.min.css & plugins.min.css for better website load performance and remove css files from above) -->
    <!-- <link rel="stylesheet" href="{{ asset('frontend/assets/css/vendor/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/plugins/plugins.min.css') }}"> -->

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <!-- Icons Css -->
    <link href="{{ asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    {{-- <link href="{{ asset('build/css/custom.css') }}"  rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="{{ asset('build/css/custom.min.css') }}?v={{ file_exists(public_path('build/css/custom.min.css')) ? filemtime(public_path('build/css/custom.min.css')) : time() }}">

</head>
@include('frontend.layouts.header')
@yield('content')
@include('frontend.layouts.footer')