<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

    <head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{ \App\Support\Brand::name() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ \App\Support\Brand::name() }}" name="description" />
    <meta content="{{ \App\Support\Brand::name() }}" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:image" itemprop="image"
      content="{{ \App\Support\Brand::logoUrl() }}">
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ \App\Support\Brand::logoUrl() }}">
        @include('layouts.head-css')
  </head>

    @yield('body')

    @yield('content')

    @include('layouts.vendor-scripts')
    </body>
</html>
