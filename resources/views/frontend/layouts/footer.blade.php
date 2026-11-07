<!--====================  footer area ====================-->
<div class="footer-area-wrapper reveal-footer bg-secondary-light">
    <div class="footer-area section-space--ptb_80">
        <div class="container">
            <div class="row footer-widget-wrapper border-bottom">
                <div class="col-lg-4 col-md-6 col-sm-6 footer-widget">
                    <h6 class="footer-widget__title mb-20 text-dark-80">Company</h6>
                    <ul class="footer-widget__list">
                        <li class="text-dark-80">RebueText, Nairobi Kenya</li>
                        <li><a href="mailto:info@rebuetext.com" class="hover-style-link text-dark-80">info@rebuetext.com</a></li>
                        <li><a href="tel:123344556" class="hover-style-link text-dark-80 font-weight--bold">(+254)7 412 26 412</a></li>

                    </ul>
                    <ul class="list ht-social-networks large-icon mt-20">
                        <li class="item">
                            <a href="#" aria-label="Twitter" class="ps-0 social-link hint--bounce hint--top hint--primary text-dark-80" target="_blank"> <i class="fab fa-twitter social-link-icon"></i> </a>
                        </li>
                        <li class="item">
                            <a href="#" target="_blank" aria-label="Instagram" class="social-link hint--bounce hint--top hint--primary text-dark-80"> <i class="fab fa-instagram social-link-icon"></i> </a>
                        </li>
                        <li class="item">
                            <a href="#" target="_blank" aria-label="Dribbble" class="social-link hint--bounce hint--top hint--primary text-dark-80"> <i class="fab fa-dribbble social-link-icon"></i> </a>
                        </li>
                        <li class="item">
                            <a href="#" target="_blank" aria-label="Pinterest" class="social-link hint--bounce hint--top hint--primary text-dark-80"> <i class="fab fa-pinterest social-link-icon"></i> </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 footer-widget">
                    <h6 class="footer-widget__title mb-20 text-dark-80">Our Solution</h6>
                    <ul class="footer-widget__list">
                        <li><a href="{{ route('products.bulksms') }}" class="hover-style-link text-dark-80">Bulk SMS</a></li>
                        <li><a href="{{ route('products.shortcode') }}" class="hover-style-link text-dark-80">Shortcode</a></li>
                        <li><a href="{{ route('products.ussd') }}" class="hover-style-link text-dark-80">USSD</a></li>
                        <li><a href="{{ route('products.airtime') }}" class="hover-style-link text-dark-80">Airtime</a></li>
                        <li><a href="{{ route('products.voice') }}" class="hover-style-link text-dark-80">Voice</a></li>
                        <li><a href="{{ route('products.whatsapp') }}" class="hover-style-link text-dark-80">WhatsApp</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 footer-widget">
                    <h6 class="footer-widget__title mb-20 text-dark-80">Quick links</h6>
                    <ul class="footer-widget__list">
                        <li><a href="{{ route('register') }}" class="hover-style-link text-dark-80">Sign Up</a></li>
                        <li><a href="{{ route('term-of-use') }}" class="hover-style-link text-dark-80">Terms of Use</a></li>
                        <li><a href="{{ route('privacy-policy') }}" class="hover-style-link text-dark-80">Privacy Policy</a></li>
                        <li><a href="#" class="hover-style-link text-dark-80">Where to Find Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 footer-widget">
                    <h6 class="footer-widget__title mb-20 text-dark-80">Support</h6>
                    <ul class="footer-widget__list">
                        <li><a href="#" class="hover-style-link text-dark-80">Forum Support</a></li>
                        <li><a href="#" class="hover-style-link text-dark-80">Help & FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="hover-style-link text-dark-80">Contact Us</a></li>
                        <li><a href="{{ route('pricing.sms') }}" class="hover-style-link text-dark-80">SMS Pricing</a></li>
                        <li><a href="{{ route('pricing.whatsapp-api') }}" class="hover-style-link text-dark-80">Whatsapp Pricing</a></li>
                        <li><a href="#" class="hover-style-link text-dark-80">Cookies Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 footer-widget">
                    <div class="footer-widget__title section-space--mb_50"></div>
                    <ul class="footer-widget__list">
                        <li><a href="#" class="image_btn"><img class="img-fluid" src="frontend/assets/images/icons/aeroland-button-google-play.webp" alt=""></a></li>
                        <li><a href="#" class="image_btn"><img class="img-fluid" src="frontend/assets/images/icons/aeroland-button-app-store.webp" alt=""></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-copyright-area section-space--pb_30 small-mt__30">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <div class="footer-widget__logo mb-30">
                        <img src="{{ asset('build/images/logo-dark.png') }}" width="160" class="img-fluid" alt="">
                    </div>
                    <span class="copyright-text text-dark-80">&copy; {{ date('Y') . ' ' . env('APP_NAME') }}. <a href="https://hasthemes.com/">All Rights Reserved.</a></span>
                </div>

            </div>
        </div>
    </div>
</div>
<!--====================  End of footer area  ====================-->
</div>

<!--====================  scroll top ====================-->
<a href="#" class="scroll-top" id="scroll-top">
<i class="arrow-top fas fa-chevron-up"></i>
<i class="arrow-bottom fas fa-chevron-up"></i>
</a>
<!--====================  End of scroll top  ====================-->

<!--====================  mobile menu overlay ====================-->
<div class="mobile-menu-overlay" id="mobile-menu-overlay">
<div class="mobile-menu-overlay__inner">
    <div class="mobile-menu-overlay__header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-8">
                    <!-- logo -->
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('build/images/logo-dark.png') }}" class="img-fluid" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-4">
                    <!-- mobile menu content -->
                    <div class="mobile-menu-content text-end">
                        <span class="mobile-navigation-close-icon" id="mobile-menu-close-trigger"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile-menu-overlay__body">
        <nav class="offcanvas-navigation">
            <ul>
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}"><span>Home</span></a></li>
                <li class="has-children">
                    <a href="javascript:void(0)">Company</a>
                    <ul class="sub-menu">
                        <li><a href="{{ route('company.about') }}"><span>About Us</span></a></li>
                        <li><a href="#"><span>Team</span></a></li>
                        <li><a href="#"><span>FAQs</span></a></li>
                        <li><a href="#"><span>Careers</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#">Solutions</a>
                    <ul class="sub-menu">
                        <li class="{{ request()->routeIs('products.bulksms') ? 'active' : '' }}"><a href="{{ route('products.bulksms') }}"><span>Bulk SMS</span></a></li>
                        <li class="{{ request()->routeIs('products.whatsapp') ? 'active' : '' }}"><a href="{{ route('products.whatsapp') }}"><span>WhatsApp</span></a></li>
                        <li class="{{ request()->routeIs('products.ussd') ? 'active' : '' }}"><a href="{{ route('products.ussd') }}"><span>USSD</span></a></li>
                        <li class="{{ request()->routeIs('products.shortcode') ? 'active' : '' }}"><a href="{{ route('products.shortcode') }}"><span>Shortcode</span></a></li>
                        <li class="{{ request()->routeIs('products.voice') ? 'active' : '' }}"><a href="{{ route('products.voice') }}"><span>Voice</span></a></li>
                        <li class="{{ request()->routeIs('products.airtime') ? 'active' : '' }}"><a href="{{ route('products.airtime') }}"><span>Airtime</span></a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('blogs*') ? 'active' : '' }}"><a href="{{ route('blogs.home') }}"><span>Blog</span></a></li>
                <li class="{{ request()->routeIs('pricing.sms') ? 'active' : '' }}"><a href="{{ route('pricing.sms') }}"><span>Bulk SMS Pricing</span></a></li>
                <li class="{{ request()->routeIs('pricing.whatsapp-api') ? 'active' : '' }}"><a href="{{ route('pricing.whatsapp-api') }}"><span>WhatsApp Pricing</span></a></li>
                <li class=""><a href="{{ route('home') }}/plugins"><span>Plugins</span></a></li>
                <li class=""><a href="{{ route('home') }}/docs"><span>API Docs</span></a></li>
                <li class="mt-3">
                    <a href="{{ route('contact') }}" class="ht-btn ht-btn-md ht-btn--outline ht-btn--circle me-2 mb-2">Talk to Us</a>
                    @guest
                        <a href="{{ route('login') }}" class="ht-btn ht-btn-md ht-btn--circle me-2">Login</a>
                    @endguest

                    @auth
                        <a href="{{ route('root') }}" class="ht-btn ht-btn-md ht-btn--circle ht-btn--outline2 me-2">Dashboard</a>
                    @endauth
                </li>
            </ul>
        </nav>
    </div>
</div>
</div>
<!--====================  End of mobile menu overlay  ====================-->

<!--====================  search overlay ====================-->
<div class="search-overlay" id="search-overlay">

<div class="search-overlay__header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 ms-auto col-4">
                <!-- search content -->
                <div class="search-content text-end">
                    <span class="mobile-navigation-close-icon" id="search-close-trigger"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="search-overlay__inner">
    <div class="search-overlay__body">
        <div class="search-overlay__form">
            <form action="{{ route('blogs.search') }}" method="GET">
                <input type="text" name="query" placeholder="Search">
            </form>
        </div>
    </div>
</div>
</div>
<!--====================  End of search overlay  ====================-->

<!-- JS
============================================ -->
<!-- Modernizer JS -->
<script src="{{ asset('frontend/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>

<!-- jQuery JS -->
<script src="{{ asset('frontend/assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('frontend/assets/js/vendor/bootstrap.min.js') }}"></script>

<!-- wow JS -->
<script src="{{ asset('frontend/assets/js/plugins/wow.min.js') }}"></script>

<!-- Swiper Slider JS -->
<script src="{{ asset('frontend/assets/js/plugins/swiper.min.js') }}"></script>

<!-- Light gallery JS -->
<script src="{{ asset('frontend/assets/js/plugins/lightgallery.min.js') }}"></script>

<!-- Waypoints JS -->
<script src="{{ asset('frontend/assets/js/plugins/waypoints.min.js') }}"></script>

<!-- Counter down JS -->
<script src="{{ asset('frontend/assets/js/plugins/countdown.min.js') }}"></script>

<!-- Isotope JS -->
<script src="{{ asset('frontend/assets/js/plugins/isotope.min.js') }}"></script>

<!-- Masonry JS -->
<script src="{{ asset('frontend/assets/js/plugins/masonry.min.js') }}"></script>

<!-- ImagesLoaded JS -->
<script src="{{ asset('frontend/assets/js/plugins/images-loaded.min.js') }}"></script>

<!-- Wavify JS -->
<script src="{{ asset('frontend/assets/js/plugins/wavify.js') }}"></script>

<!-- jQuery Wavify JS -->
<script src="{{ asset('frontend/assets/js/plugins/jquery.wavify.js') }}"></script>

<!-- Circle progress JS -->
<script src="{{ asset('frontend/assets/js/plugins/circle-progress.min.js') }}"></script>

<!-- Counterup JS -->
<script src="{{ asset('frontend/assets/js/plugins/counterup.min.js') }}"></script>

<!-- Animation text JS -->
<script src="{{ asset('frontend/assets/js/plugins/animation-text.min.js') }}"></script>

<!-- Vivus JS -->
<script src="{{ asset('frontend/assets/js/plugins/vivus.min.js') }}"></script>

<!-- Some plugins JS -->
<script src="{{ asset('frontend/assets/js/plugins/some-plugins.js') }}"></script>

<!-- Plugins JS (Please remove the comment from below plugins.min.js for better website load performance and remove plugin js files from above) -->
<!--         
<script src="{{ asset('frontend/assets/js/plugins/plugins.min.js') }}"></script> 
-->

<!-- Main JS -->
<script src="{{ asset('frontend/assets/js/main.js') }}"></script>

<!-- Chat on Whatsapp -->
{{-- <script src="https://whatsapp.rebuetext.com/popup/whatsapp?id=el3ywA7j02"></script><div id="embed-whatsapp-chat"></div>     --}}

<!-- RebueText Chat on Whatsapp -->
<script src="https://rebuetext.com/widget/wa_0cfcf2cf2dd91259b525737284737fe2/script?v=1773851979"></script>
                        

</body>

</html>