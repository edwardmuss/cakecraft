@extends('frontend.layouts.app')

@section('title', 'RebueText Privacy Policy | Integrate with your apps')
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
                            <h1 class="mb-15 text-white">RebueText Privacy Policy</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-delivering-wrapper section-space--ptb_100">
            <div class="container">
                <div class="row">
                    <h1>Privacy Policy</h1>

                    <h2>1. IMPORTANT INFORMATION AND WHO WE ARE</h2>
                    <p>RebueText is the controller and responsible for your personal data.</p>

                    <h3>CONTACT DETAILS</h3>
                    <p>Our full details are:</p>
                    <p>
                        <strong>Company Name:</strong> RebueText<br>
                        <strong>Email address:</strong> <a href="mailto:info@cloudrebue.co.ke">info@cloudrebue.co.ke</a><br>
                        <strong>Telephone number:</strong> +254741226412
                    </p>

                    <h2>2. THE DATA WE COLLECT ABOUT YOU</h2>
                    <p>We may collect, use, store and transfer different kinds of personal data about you as follows:</p>
                    <ul>
                        <li>Identity Data (including name and profile imagery).</li>
                        <li>Contact Data (including email addresses and phone numbers).</li>
                        <li>Transaction Data.</li>
                        <li>Technical Data (including OAuth tokens).</li>
                        <li>Profile Data.</li>
                        <li>Usage Data.</li>
                        <li>Marketing and Communications Data.</li>
                    </ul>

                    <h2>3. HOW WE USE YOUR PERSONAL DATA</h2>
                    <p>We will only use your personal data for the purpose for which we collected it which include the following:</p>
                    <ul>
                        <li>To register you as a new customer.</li>
                        <li>To process and deliver your order or service requests.</li>
                        <li>To manage your relationship with us.</li>
                        <li>To facilitate third-party integrations (such as Google Sheets) based on your explicit configurations.</li>
                        <li>To improve our website, products/services, marketing or customer relationships.</li>
                    </ul>

                    <h2>4. HOW LONG WILL YOU USE MY PERSONAL DATA FOR?</h2>
                    <p>We will only retain your personal data for as long as reasonably necessary to fulfil the purposes we collected it for, including for the purposes of satisfying any legal, regulatory, tax, accounting or reporting requirements. We may retain your personal data for a longer period in the event of a complaint or if we reasonably believe there is a prospect of litigation in respect to our relationship with you.</p>

                    <h2>5. HOW WE SHARE YOUR DATA</h2>
                    <p>We only share aggregated Data such as statistical or demographic data with third parties. Aggregated Data could be derived from your personal data but is not considered personal data in law as this data will not directly or indirectly reveal your identity. We do not sell your personal data to third parties.</p>

                    <hr class="my-5">

                    <h2>6. GOOGLE API SERVICES USER DATA POLICY</h2>
                    <p>RebueText's use and transfer to any other app of information received from Google APIs will adhere to the <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank">Google API Services User Data Policy</a>, including the Limited Use requirements. Specifically, regarding Google user data:</p>

                    <ul>
                        <li><strong>Data Accessed:</strong> If you choose to connect your Google account, RebueText will request access to your Google Profile (Email and Name), Google Drive file metadata (Read-Only access to file names and IDs), and Google Spreadsheets (Read and Write access). We do not access your Google passwords.</li>
                        
                        <li><strong>Data Usage:</strong> Your Google data is used strictly to provide the core functionalities of our automation builder. Your email and profile name are used for account identification. Drive metadata is used to populate a user-interface dropdown so you can select a specific spreadsheet. Spreadsheet Read/Write access is used exclusively to append incoming chatbot data (e.g., saving a lead to a sheet) or read data (e.g., checking a cell for an automated reply) based on the exact workflows you manually configure in our builder. <strong>We do not use Google Workspace APIs to develop, improve, or train generalized AI and/or machine learning models.</strong></li>
                        
                        <li><strong>Data Sharing:</strong> We do not sell, rent, or share your Google user data with any third-party marketing, advertising, or data-broker services. Data read from or written to your Google Sheets is processed securely between RebueText and Google's servers and is never shared externally unless explicitly required by law.</li>
                        
                        <li><strong>Data Storage & Protection:</strong> All Google data, including OAuth 2.0 access and refresh tokens, is encrypted in transit using SSL/TLS and encrypted at rest in our secure databases. We employ strict access controls and regular security audits to prevent unauthorized access.</li>
                        
                        <li><strong>Data Retention & Deletion:</strong> Google OAuth tokens and related integration metadata are retained only as long as your RebueText account remains active and the integration is authorized. You may revoke our access at any time directly from your Google Account settings or by disconnecting the integration within the RebueText dashboard. Upon account deletion or integration removal, all associated Google OAuth tokens and cached integration data are permanently deleted from our servers.</li>
                    </ul>

                    <hr class="my-5">

                    <h2>7. FURTHER DETAILS</h2>
                    <p>If you are looking for more information on how we process your personal data including on data security, data retention and lawful processing bases, please contact us at <a href="mailto:info@cloudrebue.co.ke">info@cloudrebue.co.ke</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection