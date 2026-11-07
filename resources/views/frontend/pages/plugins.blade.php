@extends('frontend.layouts.app')

@section('title', 'RebueText Bulk SMS WordPress Plugin | Integrate with your apps')
@section('keywords', "Reach your audience instantly with RebueText, the powerful Bulk SMS WordPress Plugin. Designed to help you send targeted messages, updates, and promotions directly to your contacts' mobile phones, seamlessly integrated into your WordPress site.")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/voice.jpg'))

@section('content')
<div class="site-wrapper-reveal">
    <div class="bg-white">
        <div class="about-banner-wrap banner-space bg-img" data-bg="frontend/assets/images/banners/voice-banner.jpg" style="background-image: url('frontend/assets/images/banners/voice-banner.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 m-auto">
                        <div class="about-banner-content text-center">
                            <h1 class="mb-15 text-white">RebueText WordPress Plugin</h1>
                            <h5 class="font-weight--normal text-white">Reach your audience instantly with RebueText, the powerful Bulk SMS WordPress Plugin.</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--===========  feature-large-images-wrapper  Start =============-->
        <div class="feature-large-images-wrapper section-space--ptb_100">
            <div class="container">
                <div class="cybersecurity-about-box">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="conact-us-wrap-one managed-it">
                                <h5 class="heading ">Designed to help you send automated<span class="text-color-primary"> alerts, order updates, and notifications</span> directly to your customers via SMS and WhatsApp.</h5>
                            </div>
                        </div>
                        <div class="col-lg-7 offset-lg-1">
                            <div class="cybersecurity-about-text">
                                <div class="text">RebueText is the ultimate solution for e-commerce stores and agencies. Seamlessly route automated notifications through SMS or Meta's Official WhatsApp API without writing a single line of code.</div>
                                <div class="button-group-wrap">
                                    <a href="{{ route('root') }}" class="ht-btn ht-btn-md me-md-3 mb-20">Try it today!</a>
                                    <a href="https://wordpress.org/plugins/rebuetext/" target="_blank" class="ht-btn ht-btn-md ht-btn--solid">Download Plugin</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--===========  feature-large-images-wrapper  End =============-->

        <!--===========  feature-images-wrapper  Start =============-->
        <div class="feature-images-wrapper bg-gray section-space--ptb_100">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-wrap text-center section-space--mb_30">
                            <h3 class="heading">Why Choose RebueText<span class="text-color-primary"> WordPress Plugin?</span></h3>
                        </div>
                        </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="feature-images__four">
                            <div class=" row">
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-chat-smile-2-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">WooCommerce Alerts</h5>
                                                <div class="text">Automatically notify customers and admins when an order status changes to Processing, Completed, or On Hold.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-chat-forward-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Form Integrations</h5>
                                                <div class="text">Natively integrates with Contact Form 7 and Gravity Forms to alert your sales team of new leads instantly.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                               <i class="ri-flashlight-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">WhatsApp Templates</h5>
                                                <div class="text">Securely syncs your approved Meta WhatsApp templates directly to your WordPress dashboard for easy mapping.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-scales-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Smart Merge Tags</h5>
                                                <div class="text">Use our click-to-insert UI to inject dynamic tags like customer names straight into your messages.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-quill-pen-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Omnichannel Routing</h5>
                                                <div class="text">Configure distinct routing rules. Send an SMS to the customer, but ping the admin team on WhatsApp.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-headphone-fill icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Comprehensive Logs</h5>
                                                <div class="text">Track delivery statuses, timestamps, and exact API responses directly from the built-in WordPress log viewer.</div>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                            </div>
                        </div>

                        <div class="section-under-heading text-center section-space--mt_60">Stop relying on email notifications that get lost in spam folders. <a href="{{ route('register') }}" target="_blank">Get Started</a></div>

                    </div>
                </div>
            </div>
        </div>
        <!--===========  feature-images-wrapper  End =============-->

        <!--========= About Delivering Wrapper Start ===========-->
        <div class="about-delivering-wrapper section-space--ptb_100 d-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-wrap text-center section-space--mb_20">
                            <h3 class="heading">Key Features of Our Airtime Top-Up Platform</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="delivering-optimal-wrap">
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Bulk Top-Ups </h5>
                                    <div class="desc">Send airtime to multiple recipients in one transaction, saving time and effort. </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Scheduled Top-Ups </h5>
                                    <div class="desc">Plan and automate airtime distribution to align with your campaigns. </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Customizable Amounts </h5>
                                    <div class="desc">Choose fixed denominations or set custom airtime values for your recipients. </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="delivering-optimal-wrap">
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Multi-Network Support </h5>
                                    <div class="desc">Compatible with all major telecom operators worldwide. </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">API Integration</h5>
                                    <div class="desc">Seamlessly integrate voice messaging into your existing applications. </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Detailed Transaction History </h5>
                                    <div class="desc">Access real-time reports on all airtime transactions for transparency and tracking. </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--========= About Get Started Wrapper Start ===========-->
        <div class="about-resources-wrapper">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-2 order-lg-1">
                        <div class="resources-left-box">
                            <div class="resources-inner">
                                <h6 class="sub-title mb-20">Get Started</h6>
                                <h3 class="heading">Try Our Plugin <span class="text-color-primary"> for Free Today.</span></h3>
                                <div class="button mt-30">
                                    <a href="https://wordpress.org/plugins/rebuetext/" target="_blank" class="ht-btn ht-btn-md">Download from WordPress</a>
                                    <a href="{{ route('contact') }}" class="ht-btn ht-btn-md">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2">
                        <div class="resources-right-box">
                            <div class="resources-right-inner text-center">
                                <div class="resources-images">
                                    <img class="img-fluid" src="{{ asset('frontend/assets/images/banners/voice.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--========= About Get Started Wrapper End ===========-->
        @include('frontend.includes.testimonials')

        @include('frontend.includes.clients-logo')
        
        @include('frontend.includes.contact-cta')
    </div>
</div>
@endsection