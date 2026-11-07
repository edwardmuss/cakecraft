@extends('frontend.layouts.app')

@section('title', 'Bulk SMS Services | RebueText - High-Volume Messaging for Businesses')
@section('keywords', "Reach thousands instantly with RebueText's Bulk SMS services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/whatsapp.jpg'))

@section('content')
<div class="site-wrapper-reveal">
    <div class="bg-white">
        <div class="about-banner-wrap banner-space bg-img" data-bg="frontend/assets/images/banners/whatsapp-banner.jpg" style="background-image: url('frontend/assets/images/banners/whatsapp-banner.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 m-auto">
                        <div class="about-banner-content text-center">
                            <h1 class="mb-15 text-white">WhatsApp Messaging</h1>
                            <h5 class="font-weight--normal text-white">Engage Your Audience on WhatsApp – The World’s Most Popular Messaging Platform</h5>
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
                                <h5 class="heading ">Take your customer<span class="text-color-primary"> communication to the next level</span> with our powerful WhatsApp messaging platform </h5>
                            </div>
                        </div>
                        <div class="col-lg-7 offset-lg-1">
                            <div class="cybersecurity-about-text">
                                <div class="text">Deliver personalized messages, respond instantly, and create meaningful connections through a channel your customers trust. Set up quick replies and chatbots to handle customer queries instantly.</div>
                                <div class="button-group-wrap">
                                    @auth
                                        <a href="{{ route('root') }}" class="ht-btn ht-btn-md me-md-3 mb-20">Go to Dashboard</a>
                                    @else
                                        <a href="{{ route('login') }}" class="ht-btn ht-btn-md me-md-3 mb-20">Get Started!</a>
                                    @endauth
                                    <a href="{{ route('pricing.whatsapp-api') }}" class="ht-btn ht-btn-md ht-btn--solid">View Pricing</a>
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
                        <!-- section-title-wrap Start -->
                        <div class="section-title-wrap text-center section-space--mb_30">
                            <h3 class="heading">Why RebueText <span class="text-color-primary"> WhatsApp Messaging?</span></h3>
                        </div>
                        <!-- section-title-wrap Start -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="feature-images__four">
                            <div class=" row">
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-check-double-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Instant Engagement</h5>
                                                <div class="text">Deliver messages directly to your audience's personal chat screen.</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-group-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Unmatched Reach </h5>
                                                <div class="text">Connect with over 2 billion active WhatsApp users worldwide.</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                               <i class="ri-server-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Secure Communication</h5>
                                                <div class="text">Safe and secure database while still maintaining its accessibility.</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-money-dollar-circle-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Cost-Effective</h5>
                                                <div class="text">Reach hundreds, thousands, or millions with affordable pricing plans</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-movie-line icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Rich Media Support</h5>
                                                <div class="text">Send images, videos, documents, and interactive messages effortlessly.</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                                <div class="single-item col-lg-4 col-md-6 mt-30 wow move-up animated" style="visibility: visible;">
                                    <!-- ht-box-icon Start -->
                                    <a href="#" class="ht-box-images style-04">
                                        <div class="image-box-wrap">
                                            <div class="box-image">
                                                <i class="ri-headphone-fill icon-large-60 text-color-secondary"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="heading">Reliable Support</h5>
                                                <div class="text">24/7 customer support to help you make the most of our platform.</div>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- ht-box-icon End -->
                                </div>
                            </div>
                        </div>

                        <div class="section-under-heading text-center section-space--mt_60">Challenges are just opportunities in disguise. <a href="{{ route('login') }}" >Get Started</a></div>

                    </div>
                </div>
            </div>
        </div>
        <!--===========  feature-images-wrapper  End =============-->

        <!--========= About Delivering Wrapper Start ===========-->
        <div class="about-delivering-wrapper section-space--ptb_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-wrap text-center section-space--mb_20">
                            <h3 class="heading">Key Features of Our WhatsApp Messaging Platform</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="delivering-optimal-wrap">
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Easy-to-Use Interface</h5>
                                    <div class="desc">Our intuitive dashboard allows you to manage campaigns, track performance, and see real-time results instantly.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Unlimited Flow Builder & Bots</h5>
                                    <div class="desc">Automate customer interactions effortlessly with a visual drag-and-drop flow builder and unlimited reply bots.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Shared Chat Inbox</h5>
                                    <div class="desc">Manage all your conversations seamlessly with a multi-agent, centralized inbox for real-time customer support.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Interactive Media & Buttons</h5>
                                    <div class="desc">Send engaging content including images, videos, documents, interactive list menus, and quick-reply buttons.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Template Sending & Campaigns</h5>
                                    <div class="desc">Broadcast approved template messages to thousands of customers and run high-converting marketing campaigns.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Flexible Scheduling</h5>
                                    <div class="desc">Schedule messages to be sent at specific dates and times, maximizing customer engagement and reach.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="delivering-optimal-wrap">
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Embedded Self-Onboarding</h5>
                                    <div class="desc">Get started in minutes with the official, seamless WhatsApp embedded signup flow for hassle-free registration.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Unlimited WABA Numbers</h5>
                                    <div class="desc">Connect and manage an unlimited number of WhatsApp Business Account profiles under a single, unified dashboard.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Unlimited Team Users</h5>
                                    <div class="desc">Scale your support and marketing operations without per-seat restrictions. Add as many team members as you need.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Webhooks & API Integration</h5>
                                    <div class="desc">Integrate WhatsApp into your existing CRM and receive real-time event updates via seamless webhook endpoints.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Advanced Reports & Insights</h5>
                                    <div class="desc">Gain valuable insights with detailed, granular analytics on delivery statuses, read rates, and overall engagement.</div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="marker"></div>
                                <div class="title-wrap">
                                    <h5 class="title">Targeted Contact Lists</h5>
                                    <div class="desc">Organize your audience with ease. Send tailored messages to specific segments using powerful list management.</div>
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
                                <h3 class="heading">Start Your WhatsApp <span class="text-color-primary"> Campaign Today.</span></h3>
                                <div class="button mt-30">
                                    <a href="https://whatsapp.rebuetext.com" target="_blank" class="ht-btn ht-btn-md">Sign Up Now</a>
                                    <a href="{{ route('contact') }}" class="ht-btn ht-btn-md">Contact Us</a>
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
        <!--========= About Get Started Wrapper End ===========-->
        @include('frontend.includes.testimonials')

        @include('frontend.includes.clients-logo')
        
        @include('frontend.includes.contact-cta')
    </div>
</div>
@endsection