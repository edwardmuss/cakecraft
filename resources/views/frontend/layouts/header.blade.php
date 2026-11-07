<body>
<div class="preloader-activate preloader-active open_tm_preloader">
        <div class="preloader-area-wrap">
            <div class="spinner d-flex justify-content-center align-items-center h-100">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div>


    <!--====================  header area ====================-->
    <div class="header-area header-area--default">

        <!-- Header Bottom Wrap Start -->
        <div class="header-bottom-wrap header-sticky">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="header default-menu-style position-relative">

                            <!-- brand logo -->
                            <div class="header__logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('build/images/logo-dark.png') }}" width="160" height="48" class="img-fluid" alt="">
                                </a>
                            </div>

                            <!-- header midle box  -->
                            <div class="header-midle-box">
                                <div class="header-bottom-wrap d-md-block d-none">
                                    <div class="header-bottom-inner">
                                        <div class="header-bottom-left-wrap">
                                            <!-- navigation menu -->
                                            <div class="header__navigation d-none d-xl-block">
                                                <nav class="navigation-menu primary--menu">

                                                    <ul>
                                                        <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}"><span>Home</span></a></li>
                                                        <li class="has-children has-children--multilevel-submenu {{ request()->routeIs('company.*') ? 'active' : '' }}">
                                                            <a href="javascript:void(0)"><span>Company</span></a>
                                                            <ul class="submenu">
                                                                <li><a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('company.about') }}"><span>About us</span></a></li>
                                                                <li><a href="#"><span>Team</span></a></li>
                                                                <li><a href="#"><span>FAQs</span></a></li>
                                                                <li><a href="#"><span>Careers</span></a></li>
                                                            </ul>
                                                        </li>
                                                        <li class="has-children has-children--multilevel-submenu {{ request()->routeIs('products*') ? 'active' : '' }}">
                                                            <a href="javascript:void(0)"><span>Solutions</span></a>
                                                            <ul class="submenu">
                                                                <li class="{{ request()->routeIs('products.bulksms') ? 'active' : '' }}"><a href="{{ route('products.bulksms') }}"><span>Bulk SMS</span></a></li>
                                                                <li class="{{ request()->routeIs('products.whatsapp') ? 'active' : '' }}"><a href="{{ route('products.whatsapp') }}"><span>WhatsApp</span></a></li>
                                                                <li class="{{ request()->routeIs('products.ussd') ? 'active' : '' }}"><a href="{{ route('products.ussd') }}"><span>USSD</span></a></li>
                                                                <li class="{{ request()->routeIs('products.shortcode') ? 'active' : '' }}"><a href="{{ route('products.shortcode') }}"><span>Shortcode</span></a></li>
                                                                <li class="{{ request()->routeIs('products.voice') ? 'active' : '' }}"><a href="{{ route('products.voice') }}"><span>Voice</span></a></li>
                                                                <li class="{{ request()->routeIs('products.airtime') ? 'active' : '' }}"><a href="{{ route('products.airtime') }}"><span>Airtime</span></a></li>
                                                            </ul>
                                                        </li>
                                                        <li class="{{ request()->routeIs('blogs*') ? 'active' : '' }}"><a href="{{ route('blogs.home') }}"><span>Blog</span></a></li>
                                                        <li class="has-children has-children--multilevel-submenu {{ request()->routeIs('pricing*') ? 'active' : '' }}">
                                                            <a href="javascript:void(0)"><span>Pricing</span></a>
                                                            <ul class="submenu">
                                                                <li class="{{ request()->routeIs('pricing.sms') ? 'active' : '' }}"><a href="{{ route('pricing.sms') }}"><span>Bulk SMS</span></a></li>
                                                                <li class="{{ request()->routeIs('pricing.whatsapp-api') ? 'active' : '' }}"><a href="{{ route('pricing.whatsapp-api') }}"><span>WhatsApp</span></a></li>
                                                            </ul>
                                                        </li>
                                                        <li class="{{ request()->routeIs('products.plugins') ? 'active' : '' }}"><a href="{{ route('products.plugins') }}"><span>Plugins</span></a></li>
                                                        <li class="{{ request()->routeIs('contact') ? 'active' : '' }}"><a href="{{ route('contact') }}"><span>Contact</span></a></li>
                                                        <li class=""><a href="{{ route('home') }}/docs" target="_blank"><span>API Docs</span></a></li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- header right box -->
                            <div class="header-right-box">
                                <div class="header-right-inner" id="hidden-icon-wrapper">

                                    {{-- <a href="{{ route('contact') }}" class="ht-btn ht-btn-md ht-btn--outline ht-btn--circle me-2">Talk to Us</a> --}}

                                    @guest
                                        <a href="{{ route('login') }}" class="ht-btn ht-btn-md ht-btn--circle me-2">Login</a>
                                    @endguest

                                    @auth
                                        <a href="{{ route('root') }}" class="ht-btn ht-btn-md ht-btn--circle me-2">Dashboard</a>
                                    @endauth
                                    
                                    {{-- <a href="{{ route('login') }}" class="btn btn--gradient">Login</a> --}}

                                    <!-- language-menu -->
                                    <div class="language-menu d-none">
                                        <ul>
                                            <li>
                                                <a href="#" class="">
                                                    <img class="ls-flag" src="{{ asset('frontend/assets/images/flags/en.webp') }}" alt="en" title="English">
                                                    <span class="wpml-ls-native">English</span>
                                                </a>

                                                <ul class="ls-sub-menu">
                                                    <li class="">
                                                        <a href="#" class="">
                                                            <img class="ls-flag" src="{{ asset('frontend/assets/images/flags/fr.webp') }}" alt="en" title="Français"><span class="wpml-ls-native">Français</span>
                                                        </a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#" class="">
                                                            <img class="wpml-ls-flag" src="{{ asset('frontend/assets/images/flags/de.webp') }}" alt="de" title="Deutsch"><span class="wpml-ls-native">Deutsch</span>
                                                        </a>
                                                    </li>

                                                </ul>

                                            </li>

                                        </ul>
                                    </div>

                                    <!-- header-search-form -->
                                    <div class="header-search-form default-search">
                                        <form action="{{ route('blogs.search') }}" class="search-form-top">
                                            <input class="search-field" type="text" name="query" placeholder="Search…">
                                            <button class="search-submit">
                                                <i class="search-btn-icon fa fa-search"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- mobile menu -->
                                <div class="mobile-navigation-icon d-block d-xl-none" id="mobile-menu-trigger">
                                    <i></i>
                                </div>
                                <!-- hidden icons menu -->
                                <div class="hidden-icons-menu d-block d-md-none" id="hidden-icon-trigger">
                                    <a href="javascript:void(0)">
                                        <i class="far fa-ellipsis-h-alt"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Bottom Wrap End -->

    </div>
    <!--====================  End of header area  ====================-->

    @yield('breadcrumb-area')
    <div id="main-wrapper">
            