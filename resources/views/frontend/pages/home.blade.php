@extends('frontend.layouts.app')

@section('title', 'RebueText | Bulk SMS, WhatsApp Messaging, Shortcodes & USSD Solutions')
@section('keywords', 'Bulk SMS services, WhatsApp messaging solutions, Shortcodes, USSD services, RebueText messaging platform, SMS marketing, business messaging solutions')
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('build/images/rebuetext-og.jpg'))

@section('content')
    <div class="site-wrapper-reveal">
        <div class="modern-it-company-top-area">

            <!--============ Modern It Company Hero Start ============-->
            <div class="modern-it-company-hero">
                <div class="container">
                    <div class="row align-items-center">
                        
                        <div class="col-lg-6 col-md-6 text-center modern-it-company-hero-image">
                            <img src="frontend/assets/images/hero/rebuetext-hero.png" class="img-fluid" alt="">
                        </div>
                        
                        <div class="col-lg-6 col-md-6">
                            <div class="modern-it-company-hero-text wow move-up section-space--pt_150">
                                <h1 class="heading">Your All-in-One <br><mark class="text-color-primary"> Messaging Solution!</mark></h1>
                                <h6 class="sort-dec">Reach your audience with ease. Powerful tools for businesses to communicate effectively</h6>

                                <div class="book-box">
                                    <div class="image">
                                        <img src="frontend/assets/images/icons/pink-curve-arrow.png" class="img-fluid" alt="">
                                    </div>
                                    <h5 class="book-text font-weight--bold mb-15">Ready to take your business communication to the next level? Sign up for a free.</h5>
                                </div>
                                
                                <div class="hero-button mt-30">
                                    <a href="{{ route('register') }}" target="_blank" class="ht-btn ht-btn-md">Sign up for free today</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--============ Modern It Company Hero End ============-->

            <!--====================  brand logo slider area ====================-->
            <div class="brand-logo-slider-area section-space--ptb_80">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-title section-space--mb_60">
                                <h5 class="heading-text text-center">Step forward to become one of <span class="text-color-primary">100+</span>
                                    successful clients of RebueText and manage customer interactions.</h5>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <!-- brand logo slider -->
                            @include('frontend.includes.clients-logo')
                        </div>
                    </div>
                </div>
            </div>
            <!--====================  End of brand logo slider area  ====================-->

            <!-- ================  Clients Review Area Start ======================= -->
            <div class="modern-it-company-clients-review-area modern-it-company-testimonial-bg section-space--pb_120">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="image text-center">
                                <img src="frontend/assets/images/general/home-graphic-1.png" class="img-fluid" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="modern-it-company-testimonial text-left">
                                <p class="text">Reach thousands instantly with RebueText's messaging services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide.</p>
                                {{-- <div class="author">
                                    <h6>Edward Muss</h6>
                                    <p>/ CEO</p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ================  Clients Review Area End ======================= -->

        </div>

        <!--=========== fun fact Wrapper Start ==========-->
        <div class="fun-fact-wrapper bg-secondary-light section-space--pb_30 section-space--pt_60">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="fun-fact--four text-center">
                            <div class="icon">
                                <i class="far fa-smile-beam"></i>
                            </div>
                            <div class="fun-fact-text">
                                <div class="fun-fact__count counter">50</div>
                                <h6 class="fun-fact__text">Happy clients</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="fun-fact--four text-center">
                            <div class="icon">
                                <i class="far fa-folder"></i>
                            </div>
                            <div class="fun-fact-text">
                                <div class="fun-fact__count counter">1000000</div>
                                <h6 class="fun-fact__text">Messages Sent</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="fun-fact--four text-center">
                            <div class="icon">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="fun-fact-text">
                                <div class="fun-fact__count counter">20</div>
                                <h6 class="fun-fact__text">WhatsApp Clients</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--=========== fun fact Wrapper End ==========-->

        <!--===========  Feature Images Wrapper Start =============-->
        <div class="feature-images-wrapper bg-gradient section-space--ptb_100">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12">
                        <!-- section-title-wrap Start -->
                        <div class="section-title-wrap text-center section-space--mb_60">
                            <h2 class="heading">Scalable, reliable, and secure <br> <span class="text-color-primary"> messaging solutions </span></h2>
                        </div>
                        <!-- section-title-wrap Start -->
                    </div>
                </div>

                <div class="image-l-r-box">
                    <div class="row">
                        <div class="col image-box-area">
                            <div class="row image-box-boder-box">
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/bulksms.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">Bulk SMS </h6>
                                                <div class="text">Send personalized messages to thousands of recipients within seconds.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.bulksms') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/whatsapp.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">WhatsApp</h6>
                                                <div class="text">Leverage the power of WhatsApp to connect directly with your audience.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.whatsapp') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/ussd.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">USSD</h6>
                                                <div class="text">Transform customer engagement with our USSD technology.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.ussd') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/shortcode.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">Shortcode</h6>
                                                <div class="text">Simplify customer interactions with easy-to-remember shortcodes.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.shortcode') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/voice.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">Voice</h6>
                                                <div class="text">Enhance your communication strategy with personalized voice messaging.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.voice') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="col-lg-4 col-md-6 image-box-boder">
                                    <!-- ht-box-icon Start -->
                                    <div class="ht-box-images style-09">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <img src="frontend/assets/images/icons/airtime.svg" width="100" class="img-fluid" alt="">
                                            </div>
                                            <div class="content">
                                                <h6 class="heading">Airtime</h6>
                                                <div class="text">Empower your business and customers with seamless airtime top-up services.
                                                </div>
                                                <div class="more-arrow-link">
                                                    <a href="{{ route('products.airtime') }}">
                                                        Learn more <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ht-box-icon End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="talk-message-box-wrapper section-space--mt_80 small-mt__60">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="talk-message-box ">
                                <div class="message-icon">
                                    <i class="far fa-comment-alt"></i>
                                </div>
                                <div class="talk-message-box-content ">
                                    <h6 class="heading">Cheers to the work that comes from trusted service providers in time.</h6>
                                    <a href="#" class="ht-btn ht-btn-sm">Let's talk</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--===========  Feature Images Wrapper End =============-->

        <!--====================  Contact us Section Start ====================-->
        <div class="contact-us-section-wrappaer processing-contact-us-bg section-space--ptb_120">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-lg-6">
                        <div class="conact-us-wrap-one">
                            <h3 class="heading text-white">Reach Out to Us – Your SMS Solution Awaits! </h3>

                            <div class="sub-heading text-white">Whether you're looking to elevate your marketing strategy, improve customer engagement, or streamline communication, don’t hesitate to get in touch.</div>

                        </div>
                    </div>

                    <div class="col-lg-6 col-lg-6">
                        <div class="contact-info-two text-center">
                            <div class="icon">
                                <span class="fas fa-phone"></span>
                            </div>
                            <h6 class="heading font-weight--reguler">Reach out now!</h6>
                            <h2 class="call-us"><a class="text-color-primary" href="tel:+254741226412">+254 741 226 412</a></h2>
                            <div class="contact-us-button mt-20">
                                <a href="#" class="btn btn--secondary">Contact us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====================  Contact us Section End  ====================-->

        <!--====================  Blog Section Start  ====================-->
        <div class="service-projects-wrapper section-space--ptb_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-wrap text-center section-space--mb_60">
                            <h6 class="section-sub-title mb-20">Latest Articles</h6>
                            <h3 class="heading">Latest articles are <span class="text-color-primary">on top all times</span></h3>
                        </div>
                    </div>
                </div>
                <div class="latest-news-wrap">
                    <div class="row">
                        {{-- Check if featured post exists --}}
                        @if($featured_post)
                        <div class="col-lg-6">
                            <div class="single-blog-lg-item wow move-up animated" style="visibility: visible;">
                                <a href="{{ route('blogs.single', $featured_post->slug) }}">
                                    <div class="post-blog-thumbnail">
                                        <img class="img-fluid" src="{{ asset('storage/'. ($featured_post->featured_image ?? 'default.jpg')) }}" alt="{{ $featured_post->title }}">
                                        <div class="post-meta mt-20">
                                            <div class="post-author">
                                                <img class="img-fluid avatar-96" src="{{ asset('favicon.ico') }}" alt=""> RebueText
                                            </div>
                                            <div class="post-date">
                                                <span class="far fa-calendar meta-icon"></span>
                                                {{-- Using optional() or ?-> for dates --}}
                                                {{ $featured_post->created_at?->format('F j, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="post-info lg-blog-post-info mt-20">
                                    <h4 class="post-title">
                                        <a href="{{ route('blogs.single', $featured_post->slug) }}">{{ $featured_post->title }}</a>
                                    </h4>
                                    <div class="post-excerpt mt-15">
                                        <p class="txt-truncate">{{ $featured_post->excerpt ?? 'No excerpt available.' }}</p>
                                    </div>
                                    <div class="btn-text mt-15">
                                        <a href="{{ route('blogs.single', $featured_post->slug) }}">Read more <i class="ml-1 button-icon fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                                </div>
                        </div>
                        @endif

                        <div class="col-lg-6 blog-list-service">
                            {{-- @forelse handles the "if empty" check automatically --}}
                            @forelse($posts as $post)
                            <div class="single-blog-lg-list wow move-up animated" style="visibility: visible;">
                                <a href="{{ route('blogs.single', $post->slug) }}">
                                    <div class="post-blog-thumbnail">
                                        <img class="img-fluid" src="{{ asset('storage/'. ($post->featured_image ?? 'default.jpg')) }}" alt="{{ $post->title }}">
                                        <div class="post-meta mt-20">
                                            <div class="post-author"></div>
                                        </div>
                                    </div>
                                </a>
                                <div class="post-info lg-blog-post-info">
                                    <div class="post-meta mb-10">
                                        <div class="post-date">
                                            <span class="far fa-calendar meta-icon"></span>
                                            {{ $post->created_at?->format('F j, Y') }} 
                                            <a href="{{ route('blogs.single', $post->slug) }}#disqus_thread"></a>
                                        </div>
                                    </div>
                                    <h5 class="post-title">
                                        <a href="{{ route('blogs.single', $post->slug) }}">{{ $post->title }}</a>
                                    </h5>
                                </div>
                                </div>
                            @empty
                                <p>No more articles found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====================  Blog Section End  ====================-->

    </div>
    <script id="dsq-count-scr" src="//cloud-rebue.disqus.com/count.js" async></script>
@endsection