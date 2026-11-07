@extends('frontend.layouts.app')

@section('title', 'About | RebueText - High-Volume Messaging for Businesses')
@section('keywords', "Reach thousands instantly with RebueText's Bulk SMS services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/whatsapp.jpg'))

@section('content')
<div class="site-wrapper-reveal">
    <div class="about-banner-wrap banner-space bg-img" data-bg="frontend/assets/images/banners/whatsapp-banner.jpg" style="background-image: url('frontend/assets/images/banners/whatsapp-banner.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 m-auto">
                    <div class="about-banner-content text-center">
                        <h1 class="mb-15 text-white">About</h1>
                        <h5 class="font-weight--normal text-white">We believe communication is the cornerstone of progress.</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===========  feature-large-images-wrapper  Start =============-->
    <div class="feature-large-images-wrapper section-space--ptb_100">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <!-- section-title-wrap Start -->
                    <div class="section-title-wrap text-center section-space--mb_60">
                        <h6 class="section-sub-title mb-20">Welcome to RebueText</h6>
                        <h3 class="heading">Empowering Communication, <br> One Connection<span class="text-color-primary"> at a Time</span></h3>
                    </div>
                    <!-- section-title-wrap Start -->
                </div>
            </div>

            <div class="cybersecurity-about-box section-space--pb_70">
                <div class="row">
                    <div class="col-lg-4 offset-lg-1">
                        <div class="modern-number-01">
                            <h2 class="heading  me-5"><span class="mark-text">5</span>Years’ Experience in IT</h2>
                            <h6 class="heading mt-30">More About Our Success Stories</h6>
                        </div>
                    </div>

                    <div class="col-lg-5 offset-lg-1">
                        <div class="cybersecurity-about-text">
                            <div class="text">Founded with the vision of bridging the gap between businesses and their audiences, we offer innovative, reliable, and scalable messaging solutions that drive engagement and deliver measurable results.</div>
                            <div class="button-text">
                                <a href="{{ route('register') }}" class="btn-text">
                                    Discover now
                                    <span class="button-icon ml-1">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="feature-images__six">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 wow move-up">
                                <!-- ht-box-icon Start -->
                                <div class="ht-box-images style-06">
                                    <div class="image-box-wrap">
                                        <div class="box-image">
                                            <div class="default-image">
                                                <img class="img-fulid" src="frontend/assets/images/icons/mitech-box-image-style-06-image-01-120x120.webp" alt="">
                                            </div>
                                        </div>
                                        <div class="content">
                                            <h5 class="heading">Our Mission</h5>
                                            <div class="text">To provide cutting-edge communication tools that empower businesses to connect with their customers effortlessly, fostering relationships and driving growth in a rapidly evolving digital world.
                                            </div>
                                            <a href="{{ route('register') }}" class="box-images-arrow">
                                                <span class="button-text">Discover now</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- ht-box-icon End -->
                            </div>
                            <div class="col-lg-4 col-md-6 wow move-up">
                                <!-- ht-box-icon Start -->
                                <div class="ht-box-images style-06">
                                    <div class="image-box-wrap">
                                        <div class="box-image">
                                            <div class="default-image">
                                                <img class="img-fulid" src="frontend/assets/images/icons/mitech-box-image-style-06-image-02-120x120.webp" alt="">
                                            </div>
                                        </div>
                                        <div class="content">
                                            <h5 class="heading">Our Vision</h5>
                                            <div class="text">To become a global leader in communication technology, setting the standard for excellence, reliability, and innovation in messaging solutions helping businesses to meet their unique business needs.
                                            </div>
                                            <a href="{{ route('register') }}" class="box-images-arrow">
                                                <span class="button-text">Discover now</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- ht-box-icon End -->
                            </div>
                            <div class="col-lg-4 col-md-6 wow move-up">
                                <!-- ht-box-icon Start -->
                                <div class="ht-box-images style-06">
                                    <div class="image-box-wrap">
                                        <div class="box-image">
                                            <div class="default-image">
                                                <img class="img-fulid" src="frontend/assets/images/icons/mitech-box-image-style-06-image-03-120x120.webp" alt="">
                                            </div>
                                        </div>
                                        <div class="content">
                                            <h5 class="heading">Our Commitment</h5>
                                            <div class="text">We are more than just a messaging platform – we are your partners in success. By providing reliable, innovative, and efficient communication tools, we aim to help you achieve your business goals.
                                            </div>
                                            <a href="{{ route('register') }}" class="box-images-arrow">
                                                <span class="button-text">Discover now</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- ht-box-icon End -->
                            </div>

                        </div>
                    </div>

                    <div class="section-under-heading text-center section-space--mt_60">Challenges are just opportunities in disguise. <a href="{{ route('register') }}">Take the challenge!</a></div>

                </div>
            </div>
        </div>
    </div>
    <!--===========  feature-large-images-wrapper  End =============-->




    <!--========= About Resources Wrapper Start ===========-->
    <div class="about-resources-wrapper">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="resources-left-box">
                        <div class="resources-inner">
                            <h6 class="sub-title mb-20">Get Started</h6>
                            <h3 class="heading">Let’s Build Connections <span class="text-color-primary"> That Matter.</span></h3>
                            <div class="button mt-30">
                                <a href="{{ route('contact') }}" class="ht-btn ht-btn-md">Let's Talk</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="resources-right-box">
                        <div class="resources-right-inner text-center">
                            <div class="resources-images">
                                <img class="img-fluid" src="{{ asset('frontend/assets/images/banners/whatsapp.jpg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--========= About Resources Wrapper End ===========-->
    <!--========= About Delivering Wrapper Start ===========-->
    <div class="about-delivering-wrapper section-space--ptb_100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-wrap text-center section-space--mb_20">
                        <h3 class="heading">We excel in delivering <br>optimal solutions.</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="delivering-optimal-wrap">
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title"> Bulk SMS </h5>
                                <div class="desc">Deliver fast, reliable, and cost-effective text messages to your audience. </div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title"> Voice Messaging </h5>
                                <div class="desc">Add a personal touch to your communication with text-speech voice calls. </div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title"> Shortcodes </h5>
                                <div class="desc">Enhance brand recognition and simplify customer interactions. </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="delivering-optimal-wrap">
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title">WhatsApp API </h5>
                                <div class="desc">Leverage the world’s most popular messaging app for business communication. </div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title"> USSD Services </h5>
                                <div class="desc">Provide interactive, real-time services for customer engagement. </div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="marker"></div>
                            <div class="title-wrap">
                                <h5 class="title"> Airtime Top-Up </h5>
                                <div class="desc">Distribute airtime seamlessly to employees, customers, or beneficiaries.. </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=========== fun fact Wrapper Start ==========-->
    @include('frontend.includes.facts')
    <!--=========== fun fact Wrapper End ==========-->

    @include('frontend.includes.testimonials')

    @include('frontend.includes.clients-logo')

</div>
@endsection