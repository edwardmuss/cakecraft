@extends('frontend.layouts.app')

@section('title', 'Contact Us | RebueText - High-Volume Messaging for Businesses')
@section('keywords', "Reach thousands instantly with RebueText's Bulk SMS services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/whatsapp.jpg'))

@section('breadcrumb-area')
<!-- breadcrumb-area start -->
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_box text-center">
                    <h2 class="breadcrumb-title">Contact us</h2>
                    <!-- breadcrumb-list start -->
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Contact us </li>
                    </ul>
                    <!-- breadcrumb-list end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb-area end -->
@endsection

@section('content')

<div class="site-wrapper-reveal">
    <!--====================  Conact us Section Start ====================-->
    <div class="contact-us-section-wrappaer section-space--pt_100 section-space--pb_70">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-lg-6">
                    <div class="conact-us-wrap-one mb-30">
                        <h3 class="heading">Have questions <br>or need assistance with <br><span class="text-color-primary">bulk SMS</span> services? </h3>
                        <div class="sub-heading">Whether you're looking to elevate your marketing strategy, improve customer engagement, or streamline communication, don’t hesitate to get in touch.</div>
                    </div>
                </div>

                <div class="col-lg-6 col-lg-6">
                    @include('includes.flash_message')
                    <div class="contact-form-wrap">
                        <form id="contact-us-form" action="{{ route('sendMail') }}" method="post">
                            @csrf
                            <div class="contact-form">
                                <div class="contact-input">
                                    <div class="contact-inner">
                                        <input name="con_name" id="con_name" type="text" placeholder="Name *">
                                    </div>
                                    <div class="contact-inner">
                                        <input name="con_email" id="con_email" type="email" placeholder="Email *">
                                    </div>
                                </div>
                                <div class="contact-inner">
                                    <input name="con_subject" id="con_subject" type="text" placeholder="Subject *">
                                </div>
                                <div class="contact-inner contact-message">
                                    <textarea name="con_message" id="con_message" placeholder="Please describe what you need."></textarea>
                                </div>
                                <!-- Google reCAPTCHA -->
                                <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>
                                <div class="submit-btn mt-20">
                                    <button id="submit-btn" class="ht-btn ht-btn-md" type="button">
                                        <span id="button-text">Send message</span>
                                        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                    <p class="form-message"></p>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================  Conact us Section End  ====================-->

    <!--====================  Conact us info Start ====================-->
    <div class="contact-us-info-wrappaer section-space--pb_100">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-6">
                    <div class="conact-info-wrap mt-30">
                        <h5 class="heading mb-20"><i class="ri-map-pin-line"></i> Address</h5>
                        <ul class="conact-info__list">
                            <li>RebueText, Nairobi, Kenya</li>
                            {{-- <li><a href="#" class="hover-style-link text-color-primary">contact.sanfrancisco@mitech.com</a></li>
                            <li><a href="#" class="hover-style-link text-black font-weight--bold">(+68)1221 09876</a></li> --}}
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="conact-info-wrap mt-30">
                        <h5 class="heading mb-20"><i class=" ri-phone-line"></i> Phone</h5>
                        <ul class="conact-info__list">
                            <li><a href="tel:+254741226412" class="hover-style-link text-black font-weight--bold">(+254)7 412 26 412</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="conact-info-wrap mt-30">
                        <h5 class="heading mb-20"><i class="ri-mail-send-line"></i> Email</h5>
                        <ul class="conact-info__list">
                            <li><a href="mailto:info@rebuetext.com" class="hover-style-link text-color-primary">info@rebuetext.com</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--====================  Conact us info End  ====================-->






    <!--========== Call to Action Area Start ============-->
    <div class="cta-image-area_one section-space--ptb_80 cta-bg-image_two">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-7 col-lg-7">
                    <div class="cta-content md-text-center">
                        <h3 class="heading">Fuel connections with RebueText<span class="text-color-primary"> Solutions today!</span></h3>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="cta-button-group--two text-center">
                        <a href="{{ route('register') }}" class="btn btn--white btn-one"><span class="btn-icon me-2"><i class="far fa-user"></i></span> Sign Up</a>
                        <a href="https://rebuetext.com" target="_blank" class="btn btn--secondary btn-two "><span class="btn-icon me-2"><i class="fab fa-whatsapp"></i></span> WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--========== Call to Action Area End ============-->




</div>
<!-- reCAPTCHA Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    document.getElementById('submit-btn').addEventListener('click', function () {
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');
        const formMessage = document.querySelector('.form-message');

        // Disable the button and show the spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        buttonText.textContent = 'Sending...';

        // Gather form data
        const formData = new FormData(document.getElementById('contact-us-form'));

        // Send AJAX request
        fetch("{{ route('sendMail') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
            .then(response => {
                // Ensure response is OK (HTTP 200)
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json(); // Parse JSON response
            })
            .then(data => {
                // Handle JSON response
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = 'Send message';

                if (data.success) {
                    formMessage.textContent = data.message;
                    formMessage.style.color = 'green';
                    document.getElementById('contact-us-form').reset();
                } else {
                    formMessage.textContent = data.message || 'Failed to send message. Please try again.';
                    formMessage.style.color = 'red';
                }
            })
            .catch(error => {
                // Handle errors
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = 'Send message';
                formMessage.textContent = 'An error occurred. Please try again later.';
                formMessage.style.color = 'red';
                console.error('Error:', error);
            });
    });
</script>


@endsection