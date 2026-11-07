@extends('frontend.layouts.app')

@section('title', 'Term of Use | RebueText, Integrate with your apps')
@section('keywords', "Reach your audience instantly with RebueText, the powerful Bulk SMS WordPress Plugin. Designed to help you send targeted messages, updates, and promotions directly to your contacts' mobile phones, seamlessly integrated into your WordPress site.")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/voice.jpg'))

@section('content')
<style>
    #terms h1, h2, h3, h4 {
        color: #2c3e50;
        margin-top: 1.5em;
    }
    #terms h1 {
        border-bottom: 2px solid var(--color--primary);
        padding-bottom: 10px;
    }
    #terms h2 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }
    .effective-date {
        background-color: var(--color--primary-light);
        padding: 15px;
        border-left: 4px solid var(--color--primary);
        margin: 20px 0;
        font-weight: bold;
    }
    .clause {
        margin-bottom: 25px;
    }
    .sub-clause {
        margin-left: 20px;
        margin-bottom: 15px;
    }
    .definitions {
        background-color: var(--color--primary-light);
        padding: 20px;
        border-radius: 5px;
        margin-top: 30px;
    }
    .definition-term {
        font-weight: bold;
        margin-top: 10px;
    }
    .contact-info {
        background-color: var(--color--primary-light);
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
    }
</style>
<div class="site-wrapper-reveal">
    <div class="bg-white">
        <div class="about-banner-wrap banner-space bg-img" data-bg="frontend/assets/images/banners/voice-banner.jpg" style="background-image: url('frontend/assets/images/banners/voice-banner.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 m-auto">
                        <div class="about-banner-content text-center">
                            <h1 class="mb-15 text-white">RebueText Term of Use</h1>
                            {{-- <h5 class="font-weight--normal text-white">Reach your audience instantly with RebueText, the powerful Bulk SMS WordPress Plugin.</h5> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--========= About Delivering Wrapper Start ===========-->
        <div class="about-delivering-wrapper section-space--ptb_100">
            <div class="container">
                <div class="row" id="terms">
                        <h1>Terms of Service</h1>
                        <div class="effective-date">EFFECTIVE DATE: 18 DECEMBER 2025</div>

                        <h2>INTRODUCTION</h2>
                        <p>This Terms of Service (or the ‘Agreement’) is a binding contract between the Client and Rebue Text, governing the use of our Services.</p>
                        <p>When we refer to “RT” or use “we”, “us” or “our”, we are referring to Rebue Text Limited and its Affiliates.</p>
                        <p>When we refer to “Client”, “you”, or “your”, we mean:</p>
                        <ul>
                            <li>the individual person who has entered into this Agreement with RT in their own capacity (such as a developer or sole user); or</li>
                            <li>the legal entity (such as a company, organisation, or sole proprietor) that has entered into this Agreement with RT, including all individuals authorised by that entity to access or use the Services on its behalf (“Authorised Users”), as described in Clause 1.2.</li>
                        </ul>
                        <p>If you are accessing the Services as an individual on behalf of a Client entity, you represent and warrant that:</p>
                        <ul>
                            <li>you are an Authorised User;</li>
                            <li>you have been validly invited or permitted by the Client to use the Services; and</li>
                            <li>you have the authority to bind the Client to this Agreement (or, if not, that the Client has already agreed to be bound).</li>
                        </ul>
                        <p>RT and the Client are each referred to in this Agreement as a “Party” and collectively as the “Parties”.</p>
                        <p>Unless otherwise specified in context, capitalised terms are defined in the Definitions section located at the end of this Agreement.</p>

                        <h2>1. ACCESS AND USE OF SERVICES</h2>
                        <div class="clause">
                            <h3>1.1 Access to Services</h3>
                            <p>To use the Services, the Client must create a User Account. The Client is solely responsible for all activities that occur under its User Account, including all API requests, which shall be deemed to have been sent and authorised by the Client.</p>
                        </div>
                        <div class="clause">
                            <h3>1.2. Authorised Users</h3>
                            <p>The Client may designate Authorised Users, including as its employees, contractors, Affiliates, or other representatives, to access the Services on its behalf. The Client acknowledges and agrees that:</p>
                            <div class="sub-clause">1.2.1 each Authorised User must create their own individual RT account to access the Services;</div>
                            <div class="sub-clause">1.2.2 the Client must ensure each Authorised User is aware of and complies with this Agreement;</div>
                            <div class="sub-clause">1.2.3 the Client remains fully responsible and liable for all acts and omissions of its Authorised Users as if they were the acts and omissions of the Client itself;</div>
                            <div class="sub-clause">1.2.4 any breach of this Agreement by an Authorised User shall be deemed a breach by the Client;</div>
                            <div class="sub-clause">1.2.5 the Client must maintain accurate records of all Authorised Users and their permission levels; and</div>
                            <div class="sub-clause">1.2.6 the Client shall promptly revoke access for any Authorised User whose authorisation ends or who no longer requires access to the Services.</div>
                        </div>
                        <div class="clause">
                            <h3>1.3 Access Rights</h3>
                            <p>Subject to the terms of this Agreement, RT grants the Client a limited, revocable, non-exclusive, non-transferable, non-sublicensable licence and right to access and use the Services solely for lawful internal business operations for the term. This license is not sublicensable or extendable to third parties without RT’s explicit written authorisation. The Client may permit individuals to access the Services as Authorised Users only in accordance with Clause 1.2.</p>
                        </div>
                        <div class="clause">
                            <h3>1.4 Permitted Use</h3>
                            <p>The Client shall use the Services only for lawful purposes, and in compliance with this Agreement, the Acceptable Use Policy, the Documentation, a Service Confirmation, and Applicable Law.</p>
                        </div>
                        <div class="clause">
                            <h3>1.5 Service Activation</h3>
                            <div class="sub-clause">1.5.1 Creating a User Account grants the Client access to the RT platform but does not automatically activate any paid Services. To use specific Services, the Client shall activate a Service through the Dashboard using the self-service options.</div>
                            <div class="sub-clause">1.5.2 Service activation shall be deemed confirmed upon: (a) the Client’s acceptance of a Service activation and a Service Confirmation is shared on the Dashboard, or (b) first use of the activated Service, whichever comes first.</div>
                            <div class="sub-clause">1.5.3 Upon Service activation applicable fees begin accruing, Service-specific terms and SLAs apply and the Client shall maintain sufficient Wallet balance for the activated Services.</div>
                            <div class="sub-clause">1.5.4 The Client may activate additional Services at any time subject to: (a) acceptance of any additional Service-specific terms; (b) payment of applicable setup fees; and (c) compliance with technical prerequisites.</div>
                            <div class="sub-clause">1.5.5 When activating Services that include dedicated resources (shortcodes, USSD codes, phone numbers), the Client shall declare whether: (a) the Client is the Beneficial Owner (using resources for its own business); or (b) a third party is the Beneficial Owner (Client is acting as technical service provider). Where a third party is the Beneficial Owner, the Client:
                                <ul>
                                    <li>shall provide the Beneficial Owner's full legal name, registration details, and contact information during the Service request;</li>
                                    <li>may be required to upload written authorisation from the Beneficial Owner; and</li>
                                    <li>acknowledges that the Beneficial Owner retains ultimate rights to the resource and acknowledges that RT may be required to transfer resource control to the Beneficial Owner if the service relationship ends.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="clause">
                            <h3>1.6 Test Accounts</h3>
                            <p>RT may provide a sandbox or a test account strictly for non-productive testing, demonstration and evaluation purposes of our Services. The Client shall not connect the test account to any productive IT environment, or use the test account for any commercial, operational, or public-facing purposes. The Client shall comply with all specific instructions, protocols, and limitations provided. Breach of these conditions may result in immediate account revocation and potential liability for damages. We reserve the right to modify or withdraw access to the test account at any time. Use of the test services is at the Client’s own risk, and we shall not be liable for any interruptions or for any data loss or damage.</p>
                            <p>For our Voice product, we offer a two (2) week test period to allow the Client to address any technical complexities in connecting to our Services. This test period begins on the date the Client’s test account is activated.</p>
                        </div>
                        <div class="clause">
                            <h3>1.7 Suspension of Services</h3>
                            <div class="sub-clause">1.7.1 RT may suspend the Services with immediate effect and will provide written notice within twenty-four (24) hours after suspension where:
                                <ul>
                                    <li>(a) the Wallet balance is negative or otherwise insufficient to cover fees;</li>
                                    <li>(b) unusual, excessive, or potentially fraudulent traffic is detected;</li>
                                    <li>(c) any use of the Services threatens the security, integrity, or availability of the Services, RT’s systems, or those of a Network Provider;</li>
                                    <li>(d) RT is required to do so by a Network Provider, Regulator, or law-enforcement authority;</li>
                                    <li>(e) the Client or an Authorised User breaches the Acceptable Use Policy in a manner that could cause immediate harm to RT, a Network Provider, or any third party;</li>
                                    <li>(f) RT reasonably believes suspension is necessary to prevent an imminent violation of Applicable Law; or</li>
                                    <li>(g) emergency maintenance or security patches must be deployed.</li>
                                </ul>
                            </div>
                            <div class="sub-clause">1.7.2 For all other material breaches of this Agreement, RT shall give the Client written notice before suspension, specifying: (a) the nature of the breach; (b) the remedial action required; and (c) the timeline for compliance (which shall be at least forty-eight (48) hours unless circumstances reasonably require a shorter period).</div>
                            <div class="sub-clause">1.7.3 Every suspension notice, whether issued before or after suspension. shall state the reason for suspension and the steps required for restoration. RT will resume the Services once the cause of suspension has been satisfactorily remedied. Where applicable, reconnection fees or other charges may be deducted directly from the Wallet in accordance with Clause 3 (Payment Terms). RT may terminate this Agreement if the Client fails to remedy the cause within thirty (30) days, or such shorter period as may be set out in the notice.</div>
                            <div class="sub-clause">1.7.4 Provided RT acts reasonably and in good faith, RT shall not be liable for losses arising from a suspension effected in accordance with this Clause 1.8. The Client remains liable for all charges incurred up to the time of suspension.</div>
                        </div>

                        <h2>2. GENERAL OBLIGATIONS OF THE CLIENT</h2>
                        <div class="clause">
                            <h3>2.1 Legal Capacity</h3>
                            <p>The Client represents and warrants that it has the legal capacity and authority to enter into this Agreement, which is a binding and enforceable. Where the Client is a legal entity, the individual accepting this Agreement represents and warrants that they have authority to bind that entity. The Client further warrants that it has authority to designate Authorised Users and shall ensure that each Authorised User acts within the scope of such authorisation.</p>
                        </div>
                        <div class="clause">
                            <h3>2.2 Security Responsibilities</h3>
                            <p>The Client is solely responsible for ensuring the security of its systems and shall implement and maintain industry standard security measures to prevent unauthorised access to or use of the Services and promptly notify RT of such incidents. The Client warrant and represent that it shall:</p>
                            <div class="sub-clause">2.2.1 maintain two-factor authentication (2FA) for its User Account;</div>
                            <div class="sub-clause">2.2.2 keep its password, API Key and User Account information confidential and secure and shall immediately notify us if the Client become aware or reasonably suspect that a password or API Key has been compromised;</div>
                            <div class="sub-clause">2.2.3 ensure only Authorised Users access the Services through the Client's User Account, that each Authorised User complies with this Agreement, and that no unauthorised sharing of credentials occurs between Authorised Users;</div>
                            <div class="sub-clause">2.2.4 secure its internal local area network and related security setups against cyber threats such as hackers and malicious intrusion. The Client shall ensure that its local networks are free from any viruses, Trojan horses, worms and other malicious software at all times;</div>
                            <div class="sub-clause">2.2.5 implement and maintain appropriate disaster recovery and business continuity in respect of its equipment. The Client shall also ensure that there are adequate back up arrangements for all Client Data which shall be tested and reviewed periodically;</div>
                            <div class="sub-clause">2.2.6 conduct regular security audits of its systems and promptly address any vulnerabilities identified;</div>
                            <div class="sub-clause">2.2.7 promptly install all security updates and patches for any software or systems used in connection with the Services; and</div>
                            <div class="sub-clause">2.2.8 notify us of any security incidents or breaches within twenty-four (24) hours of discovery.</div>
                        </div>
                        <div class="clause">
                            <h3>2.3 Cooperation</h3>
                            <p>The Client shall promptly comply with requests, directions or orders from RT, a Network Provider or a Regulator that is necessary to:</p>
                            <div class="sub-clause">2.3.1 ensure conformity of a Service to any requirement or determination,</div>
                            <div class="sub-clause">2.3.2 enable RT to provide support, maintenance, diagnostics, or upgrades; or</div>
                            <div class="sub-clause">2.3.3 ensure compliance with the Agreement.</div>
                        </div>
                        <div class="clause">
                            <h3>2.4 Liability for Content</h3>
                            <p>The Client is solely responsible for all content transmitted through the Services by it or any of its Authorised Users. RT does not control or monitor such content. The Client acknowledges that content created or transmitted by any Authorised User shall be deemed to be content of the Client. To the fullest extent permitted by law. To the fullest extent permitted by law, we exclude all liability for any issues or claims arising from the content, including but not limited to errors, viruses, or security breaches that may occur, and any claims arising due to the use or misuse of such content, except in cases of our gross negligence or wilful misconduct. The Client agrees to indemnify and hold RT harmless from any third-party claims arising from its content.</p>
                        </div>
                        <div class="clause">
                            <h3>2.5 Client Information</h3>
                            <p>The Client agrees to provide information required for the Service provision, and to promptly update this information upon request including:</p>
                            <div class="sub-clause">2.5.1 identification documentation for legal and regulatory compliance;</div>
                            <div class="sub-clause">2.5.2 a data protection authority letter;</div>
                            <div class="sub-clause">2.5.3 relevant permits, licences or authorisation for Service use; and</div>
                            <div class="sub-clause">2.5.4 any other information reasonably requested by us, a Network Provider or Regulator.</div>
                            <p>The Client warrants that all information provided shall be true, accurate, and complete, and the Client agree to update such information promptly if it changes.</p>
                        </div>
                        <div class="clause">
                            <h3>2.6 Third-Party Services and Applications</h3>
                            <p>The Client may choose to integrate with third-party web services, software and/or application when accessing our Services. The Client is solely responsible for these integrations, including compliance with their terms and obtaining necessary licenses and consents, maintaining and promptly updating any third-party services or applications integrated with our Services to ensure continued compatibility and security. RT shall not be liable for any issues caused by these third-party services, including compatibility problems, updates, errors, or bugs arising from the Client’s use. The Client shall indemnify, defend, and hold RT harmless from and against any third-party claims, losses, damages, liabilities, and expenses (including reasonable attorneys' fees) arising from malfunctions, defects, or incompatibilities of any equipment or software the Client provides, or uses in connection with the Services.</p>
                        </div>
                        <div class="clause">
                            <h3>2.7 Compliance with Laws</h3>
                            <p>The Client warrant that it shall:</p>
                            <div class="sub-clause">2.7.1 obtain and maintain all necessary permits, consents, rights, authorisations, or certifications required for its use of the Services, and provide proof of compliance upon request;</div>
                            <div class="sub-clause">2.7.2 comply with applicable Data Protection laws when handling or processing any personal data in connection with the Services;</div>
                            <div class="sub-clause">2.7.3 ensure that its use of the Service does not violate any legal obligations or infringe on any rights of third parties; and</div>
                            <div class="sub-clause">2.7.4 provide true, accurate and complete information upon request.</div>
                        </div>

                        <h2>3. PAYMENT TERMS</h2>
                        <div class="clause">
                            <h3>3.1 Fees</h3>
                            <p>Unless otherwise agreed in writing, all fees are payable in advance. The Client shall pay all applicable Service fees, maintenance fees, one-time setup charges, and any other charges that apply under this Agreement. All such fees will be automatically deducted from the Client’s Wallet balance. Such payments shall be without any set-off, counterclaim, deduction or withholding, except for any mandatory deduction or withholding of Taxes as required by law.</p>
                        </div>
                        <div class="clause">
                            <h3>3.2 Wallet Top-Up</h3>
                            <p>The Client shall maintain a positive Wallet balance at all times. Wallet top-ups may be made using any available method as listed on RT’s Website or in the Dashboard. Top-ups will be deposited in the currency selected during the User Account creation. The exchange rate used for top-ups in a currency different from the User Account currency will be the prevailing market rate at the time of the transaction, which may be subject to fluctuations.</p>
                        </div>
                        <div class="clause">
                            <h3>3.3 Recurring Fees Deduction Schedule</h3>
                            <div class="sub-clause">3.3.1 Recurring fees - including, without limitation, maintenance charges for short codes, USSD codes, Voice numbers or other numbering resources - will be deducted from the Wallet on the first (1st) day of each calendar month.</div>
                            <div class="sub-clause">3.3.2 If the Client wishes to cancel a recurring Service and avoid the next monthly charge, the Client must submit a written cancellation request no later than the nineteenth (19th) day of the preceding month.</div>
                            <div class="sub-clause">3.3.3 If, by the first (1st) day of the month, the Wallet balance is insufficient to cover the recurring fee, RT shall deactivate the affected Service immediately. If the Wallet remains insufficient by the twentieth (20th) day of that month, RT will issue a written notice that the relevant numbering resource or Service will be disconnected from the User Account, and may be reallocated or returned to a Network Provider on the last day of that month.</div>
                            <div class="sub-clause">3.3.4 Reconnection of any suspended or disconnected Service is subject to: (a) payment of all outstanding fees, (b) restoration of the Wallet to a positive balance, and (c) payment of any applicable reconnection or new setup fees as set out in RT’s Pricing Schedule or communicated in writing at the time of reinstatement.</div>
                        </div>
                        <div class="clause">
                            <h3>3.4 Security Deposit</h3>
                            <p>RT may request a security deposit to cover potential regulatory fines, penalties, or unpaid charges arising from breach of the Acceptable Use Policy or other material terms. If any portion of the deposit is used, RT may deduct the replenishment amount from the Wallet or request immediate top-up. Failure to comply may result in suspension under Clause 1.8. RT reserves the right to adjust the security deposit based on periodic reviews of the Client’s adherence to the Acceptable Use Policy and any changes in regulatory standards or penalties that may arise.</p>
                        </div>
                        <div class="clause">
                            <h3>3.5 Taxes</h3>
                            <p>Unless stated otherwise, all fees are exclusive of Taxes. To claim a tax exemption or a specific withholding amount, the Client shall provide us with a valid tax certificate, receipt, or other official document from the relevant tax authority to support its position. Upon request, the Client agrees to provide RT with its tax identification information needed for RT’s tax compliance purposes.</p>
                        </div>
                        <div class="clause">
                            <h3>3.6 Price Changes</h3>
                            <p>RT reserve the right to change our fees under the following conditions:</p>
                            <div class="sub-clause">3.6.1 Changes due to adjustments in rates or charges by Network Providers, Regulators, or governmental actions: We will notify the Client of the new prices and the effective date as soon as we are informed; and</div>
                            <div class="sub-clause">3.6.2 All other fee changes: We will provide at least thirty (30) days' prior written notice before the new prices take effect.</div>
                            <div class="sub-clause">3.6.3 Notice will be sent via email to the User Account’s linked email address. The Client's continued use of Services after the effective date constitutes acceptance of the new pricing. The Client may terminate affected Services before new prices take effect by following the termination procedures in Clause 4.</div>
                        </div>

                        <h2>4. TERM AND TERMINATION</h2>
                        <div class="clause">
                            <h3>4.1 Term</h3>
                            <div class="sub-clause">4.1.1 This Agreement becomes effective: (a) for new Clients: upon creation of User Account on the Dashboard; and (b) for existing Clients: as of 1 August 2025.</div>
                            <div class="sub-clause">4.1.2 This Agreement continues until terminated in accordance with this Clause 4.</div>
                            <div class="sub-clause">4.1.3 Service-specific terms (including pricing, service levels, and technical requirements) only apply to a Service that the Client has activated in accordance with Clause 1.5.</div>
                            <div class="sub-clause">4.1.4 Each Service activated shall continue until: (a) terminated in accordance with the Service-specific terms; (b) terminated by either Party under Clause 4.2; or (c) this Agreement is terminated in its entirety.</div>
                        </div>
                        <div class="clause">
                            <h3>4.2 Termination for Convenience</h3>
                            <p>Either Party may terminate the Agreement for any reason on thirty (30) days’ prior written notice.</p>
                        </div>
                        <div class="clause">
                            <h3>4.3 Termination for Material Breach</h3>
                            <p>Either Party may terminate immediately by written notice if the other Party materially breaches the Agreement and:</p>
                            <div class="sub-clause">4.3.1 the breach is not capable of cure; or</div>
                            <div class="sub-clause">4.3.2 the breach is capable of cure but remains uncured fourteen (14) days after written notice specifying the breach and required cure.</div>
                        </div>
                        <div class="clause">
                            <h3>4.4 Additional Termination Rights</h3>
                            <p>RT may also terminate immediately by written notice if:</p>
                            <div class="sub-clause">4.4.1 required by a Regulator, Network Provider, or Applicable Law; or</div>
                            <div class="sub-clause">4.4.2 the Client becomes insolvent, enters administration, or ceases business.</div>
                        </div>
                        <div class="clause">
                            <h3>4.5 Effect of Expiration or Termination</h3>
                            <p>Upon the effective date of termination or expiration of this Agreement:</p>
                            <div class="sub-clause">4.5.1 RT will deactivate access to the Services and all licences will cease;</div>
                            <div class="sub-clause">4.5.2 RT will deduct any outstanding fees from the Wallet; if the Wallet is insufficient, the Client shall pay the balance within seven (7) days, after which interest accrues at the statutory maximum</div>
                            <div class="sub-clause">4.5.3 within thirty (30) days each Party shall return or delete the other Party’s Confidential Information and Client Data, except where retention is required by law; and</div>
                            <div class="sub-clause">4.5.4 provided no fees remain due, RT will refund any unused Wallet balance, and any security deposit, less bank charges and mandatory taxes.</div>
                            <p>The termination of the Agreement shall not affect any rights, remedies, obligations or liabilities of the Parties that have accrued up to the effective termination date, including the right to claim damages in respect of any breach of the Agreement which existed at or before the effective termination date.</p>
                        </div>
                        <div class="clause">
                            <h3>4.6 Survival</h3>
                            <p>Any clauses that are intended to survive termination of this Agreement shall remain in effect regardless of the cause of termination.</p>
                        </div>

                        <h2>5. CONFIDENTIALITY</h2>
                        <div class="clause">
                            <h3>5.1 Definition</h3>
                            <p>“Confidential Information” means any non-public information disclosed by one Party to the other in connection with this Agreement, whether orally, visually, in writing, or by access to systems, and whether marked as confidential or not, including but not limited to:</p>
                            <div class="sub-clause">5.1.1 API keys and authentication credentials,</div>
                            <div class="sub-clause">5.1.2 technical specifications and documentation,</div>
                            <div class="sub-clause">5.1.3 Client Data, business models, or commercial strategies, and</div>
                            <div class="sub-clause">5.1.4 the existence and content of any Service Confirmation, security assessments, or Service Confirmations.</div>
                        </div>
                        <div class="clause">
                            <h3>5.2 Scope</h3>
                            <p>Each Party shall keep all Confidential Information strictly confidential and shall not disclose it to any third party without the prior written consent of the disclosing Party, except where required by Applicable Law or a valid court or regulatory order, or as expressly permitted under this Agreement.</p>
                            <p>Confidential Information excludes information that:</p>
                            <div class="sub-clause">5.2.1 is or becomes publicly available through no breach of this Agreement;</div>
                            <div class="sub-clause">5.2.2 is independently developed by the receiving Party without use of the disclosing Party’s information; or</div>
                            <div class="sub-clause">5.2.3 is rightfully received from a third party without restriction and without breach of any duty.</div>
                        </div>
                        <div class="clause">
                            <h3>5.3 Confidentiality Obligations</h3>
                            <p>The receiving Party shall:</p>
                            <div class="sub-clause">5.3.1 use the Confidential Information solely to perform its obligations or exercise its rights under this Agreement;</div>
                            <div class="sub-clause">5.3.2 limit disclosure of Confidential Information to its employees, Affiliates, Authorised Users, or subcontractors who have a legitimate need to know for the permitted purpose, and are bound by equivalent confidentiality obligations; and</div>
                            <div class="sub-clause">5.3.3 use at least the same degree of care as it uses to protect its own confidential information, but in no event less than reasonable care.</div>
                        </div>
                        <div class="clause">
                            <h3>5.4 Duration and Survival</h3>
                            <p>These confidentiality obligations shall remain in effect during the term of the Agreement and shall survive for five (5) years after its expiration or termination. For Confidential Information constituting trade secrets or Client Data, the obligations shall survive indefinitely for so long as such information remains confidential.</p>
                        </div>

                        <h2>6. INTELLECTUAL PROPERTY</h2>
                        <div class="clause">
                            <h3>6.1 RT’s Intellectual Property</h3>
                            <p>RT retains all right, title, and interest in and to its Intellectual Property, including but not limited to:</p>
                            <div class="sub-clause">6.1.1 the Services, including all software, technology, platforms, APIs, documentation, user interfaces, and any associated trademarks, logos, and brand elements;</div>
                            <div class="sub-clause">6.1.2 all pre-existing works, inventions, discoveries, creations, improvements, trade secrets, know-how, and other intellectual property rights owned or licenced by RT before or during the term of this Agreement; and</div>
                            <div class="sub-clause">6.1.3 all modifications, enhancements, or derivative works of RT’s Intellectual Property developed by RT or its agents whether developed independently or in response to Client requests.</div>
                            <p>Nothing in this Agreement grants the Client rights to, RT’s Intellectual Property except the limited access and use rights granted under Clause 2.2.</p>
                        </div>
                        <div class="clause">
                            <h3>6.2 Client’s Intellectual Property</h3>
                            <p>The Client retains all right, title, and interest in and to its own pre-existing Intellectual Property and developed independently, and nothing in this Agreement shall be construed to transfer any such rights to RT. The Client’s use of the Services does not grant RT any rights to the Client’s Intellectual Property, except as necessary to provide the Services or explicitly stated in this Agreement. The Client is responsible for ensuring that any Client Data shared does not infringe upon third-party Intellectual Property rights and complies with all Applicable Laws.</p>
                        </div>
                        <div class="clause">
                            <h3>6.3 Client Data Licence</h3>
                            <p>The Client shall own its Client Data. The Client grants RT a non-exclusive, worldwide, royalty-free, licence to right to collect, use, modify, adapt, and process the Client Data to:</p>
                            <div class="sub-clause">6.3.1 deliver, support, and improve the Services;</div>
                            <div class="sub-clause">6.3.2 conduct internal analytics and testing; and</div>
                            <div class="sub-clause">6.3.3 develop new features or capabilities that enhance the Dashboard or API Platform.</div>
                            <p>RT shall handle Client Data in accordance with Data Protection Laws and the Data Processing Agreement. RT may generate and use aggregated, anonymised, or de-identified data derived from Client Data that does not identify the Client or its end users for our business purposes, including analytics, research, benchmarking, and service improvement, and developing and offering of new products and services. Such data is no longer considered personal data under applicable law. RT retain all rights, title, and interest in any anonymised data sets, models, or derivative works created in the course of providing or improving the Services.</p>
                        </div>
                        <div class="clause">
                            <h3>6.4 Feedback</h3>
                            <p>The Client may voluntarily submit comments, suggestions, or improvement ideas regarding the Services. The Client assigns all right, title, and interest in such Feedback to RT, and RT shall be free to use it without restriction or obligation.</p>
                        </div>
                        <div class="clause">
                            <h3>6.5 Use of Logos and Trademarks</h3>
                            <p>Each Party grants the other Party a limited, non-exclusive, non-transferable, royalty-free licence to use the other Party’s logo and trademarks (collectively the "Marks") solely for the purpose of referencing the other Party as a client or service provider in marketing materials, websites, or case studies. The use of the Marks shall:</p>
                            <div class="sub-clause">6.5.1 comply with the brand owner’s guidelines;</div>
                            <div class="sub-clause">6.5.2 not misrepresent or disparage the other Party; and</div>
                            <div class="sub-clause">6.5.3 cease immediately upon written request or termination of this Agreement.</div>
                            <p>Any use of the Marks beyond the scope of this licence, or any use that materially deviates from the other Party’s brand guidelines, shall require the prior written consent of the other Party. This licence shall terminate automatically upon the termination of this Agreement.</p>
                        </div>
                        <div class="clause">
                            <h3>6.6 Restrictions</h3>
                            <p>The Client shall not:</p>
                            <div class="sub-clause">6.6.1 reverse engineer, decompile, disassemble, or otherwise attempt to derive the source code or underlying ideas or algorithms of the Services or any component;</div>
                            <div class="sub-clause">6.6.2 modify, copy, adapt, or create derivative works based on the Services or any component;</div>
                            <div class="sub-clause">6.6.3 sublicense, resell, allow unauthorised third parties to access the Services; or</div>
                            <div class="sub-clause">6.6.4 use the Services to build or train a competing product or service.</div>
                            <p>Any unauthorised use may result in immediate suspension or termination of access to the Services, in addition to any other remedies available to RT.</p>
                        </div>

                        <h2>7. WARRANTIES AND DISCLAIMERS</h2>
                        <div class="clause">
                            <h3>7.1 Mutual Warranties</h3>
                            <p>Each Party represents and warrants that:</p>
                            <div class="sub-clause">7.1.1 It has full legal power and authority to enter into and perform its obligations under this Agreement;</div>
                            <div class="sub-clause">7.1.2 The execution and performance of this Agreement does not violate any other agreement to which it is a party; and</div>
                            <div class="sub-clause">7.1.3 It will comply with all Applicable Laws in the performance of this Agreement.</div>
                        </div>
                        <div class="clause">
                            <h3>7.2 RT Warranties</h3>
                            <p>RT represents and warrants that:</p>
                            <div class="sub-clause">7.2.1 It will provide the Services using commercially reasonable efforts and in accordance with prevailing industry standards;</div>
                            <div class="sub-clause">7.2.2 It will implement and maintain appropriate technical and organisational measures to protect Client Data, in accordance with Applicable Data Protection Laws and the Data Processing Agreement; and</div>
                            <div class="sub-clause">7.2.3 It owns or has the right to provide the Services as described under this Agreement.</div>
                        </div>
                        <div class="clause">
                            <h3>7.3 Disclaimer of Warranties</h3>
                            <p>Except as expressly stated in this Agreement:</p>
                            <div class="sub-clause">7.3.1 The Services are provided “as is” and “as available” without warranty of any kind, whether express, implied, statutory or otherwise.</div>
                            <div class="sub-clause">7.3.2 RT specifically disclaims any implied warranties of merchantability, fitness for a particular purpose, title, non-infringement, and uninterrupted or error-free operation;</div>
                            <div class="sub-clause">7.3.3 RT does not warrant that the Services will be compatible with all Client systems, error-free, or immune from interruptions, cyberattacks, or other risks beyond RT’s control.</div>
                        </div>

                        <h2>8. LIABILITY</h2>
                        <div class="clause">
                            <h3>8.1 Indemnification</h3>
                            <p>Each Party (the "Indemnifying Party") shall indemnify, defend, and hold harmless the other Party (the "Indemnified Party") from and against any third-party claims, losses, damages, liabilities, and expenses (including reasonable attorneys' fees) arising from the Indemnifying Party’s:</p>
                            <div class="sub-clause">8.1.1 Material breach of this Agreement;</div>
                            <div class="sub-clause">8.1.2 Infringement of any third-party intellectual property rights;</div>
                            <div class="sub-clause">8.1.3 Violation of Applicable Law in connection with the Services; or</div>
                            <div class="sub-clause">8.1.4 Use or misuse of the Services by the Indemnifying Party or its Authorised Users.</div>
                        </div>
                        <div class="clause">
                            <h3>8.2 Limitation of Liability</h3>
                            <p>Except for liability arising from:</p>
                            <div class="sub-clause">8.2.1 Indemnification obligations under Clause 8.1;</div>
                            <div class="sub-clause">8.2.2 Breach of confidentiality obligations under Clause 5;</div>
                            <div class="sub-clause">8.2.3 Infringement or misappropriation of the other Party’s Intellectual Property;</div>
                            <div class="sub-clause">8.2.4 Violation of the Acceptable Use Policy; or</div>
                            <div class="sub-clause">8.2.5 Breach of Applicable Data Protection Laws;</div>
                            <p>Each Party’s total aggregate liability to the other under this Agreement, whether in contract, tort (including negligence), or otherwise, shall be limited to the total amount of fees actually paid by the Client to RT under this Agreement in the twelve (12) months immediately preceding the event giving rise to the claim.</p>
                        </div>
                        <div class="clause">
                            <h3>8.3 Exclusion of Liability</h3>
                            <p>To the fullest extent permitted by law, neither Party shall be liable to the other for any indirect, incidental, special, consequential, or punitive damages of any kind, including without limitation, loss of profits, loss of business, or loss of data, even if advised of the possibility of such damages. Nothing in this Agreement shall exclude or limit either Party’s liability for fraud or fraudulent misrepresentation, gross negligence or wilful misconduct; or any liability that cannot be excluded under Applicable Law.</p>
                        </div>

                        <h2>9. FORCE MAJEURE</h2>
                        <div class="clause">
                            <h3>9.1 Force Majeure Definition</h3>
                            <p>Neither Party is liable for delays or non-performance (except payment obligations) caused by events beyond their reasonable control, including:</p>
                            <div class="sub-clause">9.1.1 Any acts of God or nature such as earthquakes, hurricanes, floods, or other extreme weather events;</div>
                            <div class="sub-clause">9.1.2 Failures, outages, or malfunctions of essential infrastructure, including telecommunications networks, data centres, or power grids;</div>
                            <div class="sub-clause">9.1.3 Failures or disruptions of third-party services or networks on which the Services rely, where such failures are not attributable to the negligence or wilful misconduct of the affected Party;</div>
                            <div class="sub-clause">9.1.4 Changes in laws, government orders, or regulatory policies that materially impact the provision or legality of the Services;</div>
                            <div class="sub-clause">9.1.5 Strikes, lockouts, or other labour disruptions that materially impact a Party's ability to perform its obligations;</div>
                            <div class="sub-clause">9.1.6 Epidemics, pandemics, or other public health crises declared by relevant authorities; or</div>
                            <div class="sub-clause">9.1.7 Any other unforeseeable events beyond a Party's control and not arising from its fault or negligence.</div>
                        </div>
                        <div class="clause">
                            <h3>9.2 Consequences and Termination for Extended Force Majeure</h3>
                            <p>If a force majeure event continues for a continuous period exceeding thirty (30) days, either Party may terminate this Agreement by giving the other Party at least seven (7) days’ prior written notice. Termination pursuant to this Clause shall be without prejudice to any rights or obligations accrued prior to the date of termination.</p>
                        </div>

                        <h2>10. GENERAL PROVISIONS</h2>
                        <div class="clause">
                            <h3>10.1 Digital Acceptance</h3>
                            <p>This Agreement may be accepted and executed by digital means, including electronic signatures and online click-throughs, and maintained in electronic format. Such acceptance and storage shall have the same legal force and effect as a manually signed agreement or a paper-based record.</p>
                        </div>
                        <div class="clause">
                            <h3>10.2 Amendments</h3>
                            <p>We may update this Agreement from time to time. If we make material changes, we will provide the Client with at least thirty (30) days’ prior written notice before the changes take effect, unless the change is due to legal, regulatory, or urgent security requirements, in which case RT will notify the Client as soon as reasonably possible. The updated Agreement will supersede prior versions and become binding on the effective date stated in the notice. If the Client does not agree with the changes, the Client may terminate the Agreement under Clause 4.2. Continued use of the Services after the effective date shall constitute the Client’s acceptance of the updated Agreement.</p>
                        </div>
                        <div class="clause">
                            <h3>10.3 Notices</h3>
                            <p>All notices required under this Agreement shall be in writing. Notices may be delivered by email, registered mail, or personal delivery as follows:</p>
                            <div class="note mb-3">
                                <p><strong>For general and operational matters:</strong><br>
                                <a href="mailto:support@rebuetext.com">support@rebuetext.com</a> (with a copy to <a href="mailto:info@rebuetext.com">info@rebuetext.com</a>)</p>
                                <p><strong>For legal notices:</strong><br>
                                <a href="mailto:legal@rebuetext.com">legal@rebuetext.com</a><br>
                                Rebue Text</p>
                            </div>
                            <p>Notices sent by email shall be deemed effectively given upon transmission from the sender's email server, provided that the sender receives no indication of non-delivery. Notices sent by registered mail shall be deemed effectively given upon the earlier of: (a) actual receipt or (b) five (5) business days after the date of posting. Notices delivered personally shall be deemed effectively given upon actual delivery. Notices to the Client shall be sent to the email address linked to the User Account. The Client shall promptly update RT of its contact details if they are changed.</p>
                        </div>
                        <div class="clause">
                            <h3>10.4 Waiver</h3>
                            <p>No failure or delay by either Party in exercising any right, power or privilege provided by law or under this Agreement will operate as a waiver. No single or partial exercise of any right, power or remedy provided by law or under our Agreement shall prevent any future exercise of it or the exercise of any other right, power or remedy. A waiver of any right, power or remedy under this Agreement or by law is only effective if it is given in writing and is duly signed by the person waiving such right, power or remedy. Any such waiver shall apply only to the circumstances for which it is given and shall not be deemed a waiver of any subsequent breach or default.</p>
                        </div>
                        <div class="clause">
                            <h3>10.5 Severance</h3>
                            <p>If any provision of this Agreement is held to be invalid, illegal, or unenforceable by a court of competent jurisdiction, the remainder of the Agreement shall remain in full force and effect. The invalid provision shall be replaced by a valid one that most closely reflects the intent of the original.</p>
                        </div>
                        <div class="clause">
                            <h3>10.6 Succession</h3>
                            <p>This Agreement shall bind and benefit each Party and their respective successors and permitted assigns.</p>
                        </div>
                        <div class="clause">
                            <h3>10.7 Assignment</h3>
                            <p>The Client may not assign or transfer this Agreement without RT’s prior written consent, except for internal reorganisations involving Affiliates. RT may assign or subcontract its rights or obligations under this Agreement to an Affiliate or service provider, provided that RT remains liable for their acts and omissions, and ensures they are bound by obligations consistent with this Agreement, including confidentiality and data protection.</p>
                        </div>
                        <div class="clause">
                            <h3>10.8 Third Party Rights</h3>
                            <p>No third party shall have any rights to enforce or benefit from any provision of this Agreement.</p>
                        </div>
                        <div class="clause">
                            <h3>10.9 Relationship of the Parties</h3>
                            <p>The Parties are independent contractors. Nothing in this Agreement shall create an agency, partnership, joint venture, or employment relationship. Neither Party has authority to bind the other without prior written consent.</p>
                        </div>
                        <div class="clause">
                            <h3>10.10 Entire Agreement</h3>
                            <p>This Agreement consists of the following documents, which together form the entire agreement between the Client and RT and supersedes and extinguishes any and all prior written or oral agreements between the Parties:</p>
                            <div class="sub-clause">10.10.1 These Terms of Service;</div>
                            <div class="sub-clause">10.10.2 The Acceptable Use Policy;</div>
                            <div class="sub-clause">10.10.3 The Service Level Agreement;</div>
                            <div class="sub-clause">10.10.4 The Privacy Notice;</div>
                            <div class="sub-clause">10.10.5 The pricing displayed in the Dashboard at the time of Service activation; and</div>
                            <div class="sub-clause">10.10.6 Any Service Confirmations.</div>
                            <p>In case of any conflict or inconsistency between these document, the order of precedence shall be:</p>
                            <ol>
                                <li>Service Confirmation (for specific services covered);</li>
                                <li>These Terms of Service</li>
                                <li>Service-specific terms</li>
                                <li>the SLA</li>
                                <li>the AUP</li>
                                <li>the Privacy Notice</li>
                            </ol>
                            <p>All documents except Service Confirmations are available at <a href="https://rebuetext.com/term-of-use">https://rebuetext.com/term-of-use</a> and may be updated in accordance with Clause 10.2</p>
                        </div>
                        <div class="clause">
                            <h3>10.11 Good Faith</h3>
                            <p>The Parties agree to cooperate in good faith to address unforeseen situations or operational ambiguities not expressly covered by this Agreement. Any mutually agreed mechanisms for addressing such issues may be documented in an addendum or incorporated into a Service Confirmation.</p>
                        </div>
                        <div class="clause">
                            <h3>10.12 Definitions</h3>
                            <div class="definitions">
                                <div class="definition-term">Affiliate</div>
                                <p>means any entity that directly or indirectly controls, is controlled by, or is under common control with the Party;</p>
                                
                                <div class="definition-term">API Key</div>
                                <p>means the unique and secret authentication code issued to you on the User Account which the Client shall use to consume the Services;</p>
                                
                                <div class="definition-term">API Platform</div>
                                <p>means the APIs, infrastructure, and support provided by RT that enables Clients to integrate and use the Services, within their Client Application;</p>
                                
                                <div class="definition-term">Applicable Law</div>
                                <p>means all applicable laws, legislation, statutory instruments, regulations and governmental guidance with binding force relevant to the Services in the Territory;</p>
                                
                                <div class="definition-term">Authorised User</div>
                                <p>means any natural person (a) whom a Client invites or permits to access the Services under its User Account, or (b) who otherwise uses the Services on a Client's behalf, including its employees, contractors, agents and Affiliates.</p>
                                
                                <div class="definition-term">Beneficial Owner</div>
                                <p>means the entity (whether an individual or organisation) identified in the service activation request as having the ultimate right to use and control any allocated telecommunications resources (shortcodes, USSD codes, phone numbers), regardless of who technically manages such resources through the Dashboard or API Platform;</p>
                                
                                <div class="definition-term">Business Day</div>
                                <p>means a day other than a Saturday, Sunday or bank or public holiday when banks generally are open for non-automated business in the Territory;</p>
                                
                                <div class="definition-term">Client Application</div>
                                <p>means any software application, system, or website created, owned, or managed by the Client that integrates with RT's APIs to use RT's services for interacting with end users;</p>
                                
                                <div class="definition-term">Client Data</div>
                                <p>means data and other information made available by or for the Client, to RT, through the use of the Services under this Agreement;</p>
                                
                                <div class="definition-term">Control</div>
                                <p>means direct or indirect ownership of more than fifty percent (50%) of the voting interests of the subject entity, and Controlling and Controlled shall be construed accordingly;</p>
                                
                                <div class="definition-term">Dashboard</div>
                                <p>means RT's web-based management portal through which Clients manage their account, Services, users, and billing;</p>
                                
                                <div class="definition-term">Documentation</div>
                                <p>means RT's documentation, including any usage guides and policies, for the Services, the current version of which is available at <a href="{{ route('home') }}/docs">{{ route('home') }}/docs</a>;</p>
                                
                                <div class="definition-term">Network Provider</div>
                                <p>means a telecommunications operators, wholesale provider, aggregator, or routing partner that RT partners with to deliver connectivity, routing, and network access for SMS, voice, and other communication services and Network Providers shall be construed accordingly;</p>
                                
                                <div class="definition-term">Regulator</div>
                                <p>means any governmental or regulatory authority with jurisdiction over telecommunications, data privacy, and other applicable regulations relevant to RT's Services and Client usage within the Territory;</p>
                                
                                <div class="definition-term">Services</div>
                                <p>means all communication solutions provided by RT, including but not limited to SMS, voice, USSD, data and airtime and other APIs that Clients can integrate with their own systems to enable messaging, calling, or data exchange;</p>
                                
                                <div class="definition-term">Service Confirmation</div>
                                <p>means the electronic confirmation generated when the Client activates Services through the Dashboard, which includes the Services selected, applicable pricing at time of activation (if different from the website pricing), and any service-specific terms accepted;</p>
                                
                                <div class="definition-term">Territory</div>
                                <p>means a country in which RT provides the Services to the Client;</p>
                                
                                <div class="definition-term">User Account</div>
                                <p>means the account created by the Client on the Dashboard that grants access to RT's Services, tracks service usage, and enables configuration of APIs and other functionalities; and</p>
                                
                                <div class="definition-term">Wallet</div>
                                <p>means the e-wallet in the User Account to which a Client loads a value of money to utilise in respect of the Services.</p>
                            </div>
                        </div>
                        <div class="clause">
                            <h3>10.13 Governing Law and Dispute Resolution</h3>
                            <p>This Agreement shall be governed by and construed in accordance with the laws of the Republic of Kenya. The Parties shall use good faith efforts to resolve any disputes amicably. If the dispute is not resolved within thirty (30) days of initiation, either Party may refer the matter to the non-exclusive jurisdiction of the courts of Kenya.</p>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection