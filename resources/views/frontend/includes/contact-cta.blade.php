<!--============ Contact Us Area Start =================-->
<div class="contact-us-area service-contact-bg processing-contact-us-bg section-space--ptb_100">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 col-lg-6">
                <div class="conact-us-wrap-one">
                    <h3 class="heading text-white">Reach Out to Us – Your SMS Solution Awaits! </h3>

                    <div class="sub-heading text-white">Whether you're looking to elevate your marketing strategy, improve customer engagement, or streamline communication, don’t hesitate to get in touch.</div>

                </div>
            </div>

            <div class="col-lg-5 me-auto ms-auto">
                <div class="business-solution-form-wrap">
                    <div class="contact-title text-center section-space--mb_40">
                        <h5 class="mb-10">Request a Quote</h5>
                        <p class="text">It's our pleasure to have a chance to serve you.</p>
                    </div>


                    <form id="quote-form" action="" method="post">
                        <div class="contact-form__two">
                            <div class="contact-input">
                                <div class="contact-inner">
                                    <input name="con_name" type="text" placeholder="Name *" required>
                                </div>
                                <div class="contact-inner">
                                    <input name="con_email" type="email" placeholder="Email *" required>
                                </div>
                            </div>
                            <div class="contact-select">
                                <div class="form-item contact-inner">
                                    <span class="inquiry">
                        <select id="con_service" name="con_service" required>
                            <option disabled="" selected="" value="">Select Service</option>
                            <option>Bulk SMS</option>
                            <option>Shortcode</option>
                            <option>USSD</option>
                            <option>Voice</option>
                            <option>Airtime</option>
                        </select>

                    </span>
                                </div>
                            </div>
                            <div class="contact-inner contact-message">
                                <textarea name="con_message" placeholder="Please describe what you need." required></textarea>
                            </div>
                            <!-- Google reCAPTCHA -->
                            <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>
                            <div class="comment-submit-btn mt-3">
                                <button id="submit-btn" class="ht-btn ht-btn-md" type="submit">
                                    <span id="button-text">Get a quote</span>
                                    <span id="spinner" class="d-none"><i class="fa fa-spinner fa-spin"></i></span>
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
<!--============ Contact Us Area End =================-->
<!-- reCAPTCHA Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    document.getElementById('submit-btn').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default form submission

        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');
        const formMessage = document.querySelector('.form-message');

        // Disable the button and show the spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        buttonText.textContent = 'Submitting...';

        // Gather form data
        const formData = new FormData(document.getElementById('quote-form'));

        // Send AJAX request
        fetch("{{ route('submitQuote') }}", {
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
                buttonText.textContent = 'Get a quote';

                if (data.success) {
                    formMessage.textContent = data.message;
                    formMessage.style.color = 'green';
                    document.getElementById('quote-form').reset();
                } else {
                    formMessage.textContent = data.message || 'Failed to submit the quote request. Please try again.';
                    formMessage.style.color = 'red';
                }
            })
            .catch(error => {
                // Handle errors
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = 'Get a quote';
                formMessage.textContent = 'An error occurred. Please try again later.';
                formMessage.style.color = 'red';
                console.error('Error:', error);
            });
    });
</script>
