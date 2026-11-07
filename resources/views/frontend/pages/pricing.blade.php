@extends('frontend.layouts.app')

@section('title', 'Pricing | RebueText - High-Volume Messaging for Businesses')
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
                    <h2 class="breadcrumb-title">Pricing</h2>
                    <!-- breadcrumb-list start -->
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Pricing </li>
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
    <!--======== Tabs Wrapper Start ======== -->
    <div class="tabs-wrapper  section-space--ptb_100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-wrapper text-center section-space--mb_60 wow move-up">
                        <h6 class="section-sub-title mb-20">discover our pricing</h6>
                        <h3 class="section-title">We’ve been triumphing all these <span class="text-color-primary">years</span> </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 ht-tab-wrap">
                    <div class="row">
                        <div class="col-12 text-center wow move-up ">
                            <ul class="nav justify-content-center ht-tab-menu" role="tablist">
                                <li class="nav-item tab__item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#bulksms-tab">Bulk SMS</a>
                                </li>
                                <li class="nav-item tab__item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#ussd-tab">USSD</a>
                                </li>
                                <li class="nav-item tab__item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#shortcode-tab">Shortcode</a>
                                </li>
                                <li class="nav-item tab__item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#voice-tab">Voice</a>
                                </li>
                                <li class="nav-item tab__item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#airtime-tab">Airtime</a>
                                </li>
                                <li class="nav-item tab__item d-none">
                                    <a class="nav-link" data-bs-toggle="tab" href="#whatsapp-tab">WhatsApp</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content ht-tab__content wow move-up">

                        <div class="tab-pane fade show active" id="bulksms-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <span>
                                        <table>
                                            <thead>
                                                <tr><th>Telco</th>
                                                <th>Starter<br>KES 0 – 19,999</th>
                                                <th>Growth<br>KES 20,000 – 99,999</th>
                                                <th>Business<br>KES 100,000 – 399,999</th>
                                                <th>Enterprise<br>KES 400,000+</th>
                                            </tr></thead>
                                            <tbody>
                                                
                                                    <tr>
                                                        <td>Safaricom</td>
                                                        <td>KES 0.8</td>
                                                        <td>KES 0.7</td>
                                                        <td>KES 0.6</td>
                                                        <td>KES 0.4</td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>Airtel Kenya (Local Traffic)</td>
                                                        <td>KES 0.8</td>
                                                        <td>KES 0.8</td>
                                                        <td>KES 0.7</td>
                                                        <td>KES 0.6</td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>Other Kenyan Telcos</td>
                                                        <td>KES 0.8</td>
                                                        <td>KES 0.8</td>
                                                        <td>KES 0.7</td>
                                                        <td>KES 0.6</td>
                                                    </tr>
                                                    
                                            </tbody>
                                        </table>
                                        </span>
                                        <span>
                                            <h3>Sender ID</h3>
                                            <p>Sender ID set up cost for all Mobile Service Providers</p>
                                            <table>
                                                <thead>
                                                    <tr><th>Telco</th>
                                                    <th>Set Up</th>
                                                </tr></thead>
                                                <tbody>
                                                    
                                                        <tr>
                                                            <td>Bundled Safaricom & Airtel</td>
                                                            <td>KES 14,100</td>
                                                        </tr>
                                                    
                                                        <tr>
                                                            <td>Safaricom only</td>
                                                            <td>KES 8,700</td>
                                                        </tr>
                                                    
                                                        <tr>
                                                            <td>Airtel only</td>
                                                            <td>KES 8,700</td>
                                                        </tr>
                                                    
                                                        <tr>
                                                            <td>Telkom</td>
                                                            <td>KES 4,500</td>
                                                        </tr>
                                                    
                                                        <tr>
                                                            <td>Equitel</td>
                                                            <td>KES 9,000</td>
                                                        </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ussd-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="kenya tab show">
                                            <span>
                                                <h3>Shared USSD Code</h3>
                                                <h4></h4>
                                                <table>
                                                    <thead>
                                                        <tr><th>USSD Code</th>
                                                        <th>Set Up</th>
                                                        <th>Monthly Maintenance</th>
                                                    </tr></thead>
                                                    <tbody>
                                                        
                                                            <tr>
                                                                <td>*483*X# or *789*X#</td>
                                                                <td>Free</td>
                                                                <td>KES 40,000</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>*483*XX# or *789*XX#</td>
                                                                <td>Free</td>
                                                                <td>KES 30,000</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>*483*XXX# or *789*XXX#</td>
                                                                <td>Free</td>
                                                                <td>KES 20,000</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>483*XXXX# or *789*XXXX# and more</td>
                                                                <td>Free</td>
                                                                <td>KES 10,000</td>
                                                            </tr>
                                                            
                                                    </tbody>
                                                </table>
                                                <br>
                                            </span>
                                            <span>
                                                <h3>Pre-Paid costs per session (Cost is covered by the end user)</h3>
                                                <h4></h4>
                                                <p>A shared USSD code will be raised on Safaricom, Airtel and Telkom.</p>
                                                <p>The telco deducts the cost of the session directly from the end users’ airtime. We charge an amount per session as well as indicated in the table below. A compulsory deposit of KES 5,000 is required for the session cost</p>
                                                    <table>
                                                        <thead>
                                                            <tr><th>Network</th>
                                                            <th>Telco Charge to the End User</th>
                                                            <th>RebueText Infrastructure Fee</th>
                                                        </tr></thead>
                                                        <tbody>
                                                            
                                                                <tr>
                                                                    <td>Safaricom</td>
                                                                    <td>KES 1</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Airtel</td>
                                                                    <td>KES 3</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Telkom</td>
                                                                    <td>KES 1</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                        </tbody>
                                                    </table>
                                            </span>
                                            <span>
                                                <h3>Dedicated USSD Code</h3>
                                                <h4></h4>
                                                
                                                    <table>
                                                        <thead>
                                                            <tr><th>Telco</th>
                                                            <th>Test Bed</th>
                                                            <th>Set Up Cost</th>
                                                            <th>Deposit(One Off)</th>
                                                            <th>Monthy Maintenance (*XXX#)</th>
                                                            <th>Monthy Maintenance (*XXX*X#) And More</th>
                                                        </tr></thead>
                                                        <tbody>
                                                            
                                                                <tr>
                                                                    <td>Safaricom</td>
                                                                    <td>KES 17,400</td>
                                                                    <td>KES 145,000</td>
                                                                    <td></td>
                                                                    <td>KES 70,000</td>
                                                                    <td>KES 58,000</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Airtel</td>
                                                                    <td></td>
                                                                    <td>KES 116,000</td>
                                                                    <td></td>
                                                                    <td>KES 46,400</td>
                                                                    <td></td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Telkom </td>
                                                                    <td>KES 17,400</td>
                                                                    <td>KES 58,000</td>
                                                                    <td>KES 28,500</td>
                                                                    <td>KES 40,600</td>
                                                                    <td></td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Equitel</td>
                                                                    <td>KES 15,000</td>
                                                                    <td>KES 60,000</td>
                                                                    <td></td>
                                                                    <td>KES 58,000</td>
                                                                    <td></td>
                                                                </tr>
                                                                
                                                        </tbody>
                                                    </table>
                                
                                            </span>
                                            <span>
                                                <h3>Post-Paid costs per session (Cost is covered by the code owner)</h3>
                                                <h4></h4>
                                                <p>A compulsory deposit of KES 10,000 is required for the session cost</p>
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Network</th>
                                                                <th>Basic Plan<br>KES 0 - 19,999</th>
                                                                <th>Plus Plan<br>KES 20,000 - 99,999</th>
                                                                <th>Premium Plan<br>KES 100,000 - 399,999</th>
                                                                <th>Max Plan<br>KES 400,000+</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            
                                                                <tr>
                                                                    <td>Safaricom</td>
                                                                    <td>KES 1.5</td>
                                                                    <td>KES 1.5</td>
                                                                    <td>KES 1.25</td>
                                                                    <td>KES 1.25</td>
                                                                </tr>
                                                                
                                                        </tbody>
                                                    </table>
                                            </span>
                                            <span>
                                                <h3>Pre-Paid costs per session (Cost is covered by the end user)</h3>
                                                <h4></h4>
                                                <p>The telco deducts the cost of the session directly from the end users’ airtime. We charge an amount per session as well as indicated in the table below.&nbsp;A compulsory deposit of KES 10,000 is required for the session cost</p>
                                                    <table>
                                                        <thead>
                                                            <tr><th>Network</th>
                                                            <th>Telco Charge to the End User</th>
                                                            <th>RebueText Infrastructure fee</th>
                                                        </tr></thead>
                                                        <tbody>
                                                            
                                                                <tr>
                                                                    <td>Safaricom</td>
                                                                    <td>KES 1</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Airtel</td>
                                                                    <td>KES 3</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Telkom</td>
                                                                    <td>KES 1</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>Equitel</td>
                                                                    <td>KES 1</td>
                                                                    <td>KES 0.25</td>
                                                                </tr>
                                                                
                                                        </tbody>
                                                    </table>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="shortcode-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <span>
                                            <h3>Dedicated Short Code</h3>
                                            <h4></h4>
                                            <table>
                                                <thead>
                                                    <tr><th>Telco</th>
                                                    <th>Deposit</th>
                                                    <th>Set Up</th>
                                                    <th>Monthly Maintenance per Telco</th>
                                                </tr></thead>
                                                <tbody>
                                                    
                                                        <tr>
                                                            <td>Safaricom, Airtel, Equitel and Telkom</td>
                                                            <td>KES 10,000</td>
                                                            <td>Free</td>
                                                            <td>KES 15,000</td>
                                                        </tr>
                                                        
                                                </tbody>
                                            </table>
                                            <i class="table-meta">*prices are 16% VAT exclusive</i>
                                            <i class="table-meta">Raising a Toll Free Short Code attracts an extra charge of KES 5,000 (VAT exclusive)</i>
                                        </span>
                                        <span>
                                            <h3>Shared Short Code (Keyword)</h3>
                                            <h4></h4>
                                            <table>
                                                <thead>
                                                    <tr><th>Set Up</th>
                                                    <th>Monthly Maintenance</th>
                                                </tr></thead>
                                                <tbody>
                                                    
                                                        <tr>
                                                            <td>KES 5,000 + 16% VAT</td>
                                                            <td>KES 2,000 + 16% VAT</td>
                                                        </tr>
                                                        
                                                </tbody>
                                            </table>
                                            
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="voice-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3>Rebue Text Voice Pricing</h3>

                                        <h3>Phone Numbers</h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Deposit</th>
                                                    <th>Set Up</th>
                                                    <th>Monthly Maintenance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Regular (Safaricom and Airtel)</td>
                                                    <td>0</td>
                                                    <td>KES 5,000 + 16% VAT</td>
                                                    <td>KES 2,000 + 16% VAT</td>
                                                </tr>
                                                <tr>
                                                    <td>Toll Free (800 series) (Safaricom only)</td>
                                                    <td>KES 50,000</td>
                                                    <td>KES 20,000 + 16% VAT</td>
                                                    <td>KES 15,000 + 16% VAT</td>
                                                </tr>
                                                <tr>
                                                    <td>Premium Rated (Safaricom only)</td>
                                                    <td>0</td>
                                                    <td>KES 20,000 + 16% VAT</td>
                                                    <td>KES 15,000 + 16% VAT</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <h3>Local Charges</h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Cost per minute</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Regular Incoming</td>
                                                    <td>KES 0.50</td>
                                                </tr>
                                                <tr>
                                                    <td>Regular Outgoing</td>
                                                    <td>KES 2.50</td>
                                                </tr>
                                                <tr>
                                                    <td>SIP Incoming</td>
                                                    <td>KES 0.25</td>
                                                </tr>
                                                <tr>
                                                    <td>SIP Outgoing</td>
                                                    <td>KES 0.25</td>
                                                </tr>
                                                <tr>
                                                    <td>SIP Trunk</td>
                                                    <td>KES 0.50</td>
                                                </tr>
                                                <tr>
                                                    <td>Toll Free Incoming</td>
                                                    <td>KES 2.50</td>
                                                </tr>
                                                <tr>
                                                    <td>Call Conference</td>
                                                    <td>KES 0.50</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <h3>Text to Speech Charges</h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Cost per character</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Google TTS Standard</td>
                                                    <td>USD 0.000008</td>
                                                </tr>
                                                <tr>
                                                    <td>Google TTS Wavenet</td>
                                                    <td>USD 0.000032</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <h3>Calls per second (CPS) Pricing</h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Calls per second</th>
                                                    <th>Monthly cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>KES 0</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>KES 3,000 + 16% VAT</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>KES 6,000 + 16% VAT</td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>KES 9,000 + 16% VAT</td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>KES 12,000 + 16% VAT</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="airtime-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="pricing-header mb-3">
                                            <h3>Rebue Text Airtime Pricing</h3>
                                        </div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Telco</th>
                                                    <th>Basic Plan (KES)<br><small>0 - 19,999</small></th>
                                                    <th>Plus Plan (KES)<br><small>20,000 - 99,999</small></th>
                                                    <th>Premium Plan (KES)<br><small>100,000 - 399,999</small></th>
                                                    <th>Max Plan (KES)<br><small>400,000+</small></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Safaricom</td>
                                                    <td>4%</td>
                                                    <td>4%</td>
                                                    <td>5%</td>
                                                    <td>6%</td>
                                                </tr>
                                                <tr>
                                                    <td>Airtel</td>
                                                    <td>4%</td>
                                                    <td>4%</td>
                                                    <td>5%</td>
                                                    <td>6%</td>
                                                </tr>
                                                <tr>
                                                    <td>JTL</td>
                                                    <td>2%</td>
                                                    <td>2.5%</td>
                                                    <td>3.5%</td>
                                                    <td>5%</td>
                                                </tr>
                                                <tr>
                                                    <td>Equitel</td>
                                                    <td>1%</td>
                                                    <td>1.25%</td>
                                                    <td>1.75%</td>
                                                    <td>2.5%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="whatsapp-tab" role="tabpanel">
                            <div class="tab-history-wrap section-space--mt_60">
                                <div class="row">
                                    <div class="col-lg-12">
                                        
                                        <div class="pricing-header mb-3">
                                            <h3>RebueText WhatsApp Business Pricing</h3>
                                            <p>
                                                WhatsApp pricing is based on <strong>conversation categories</strong> as defined by Meta.
                                                A conversation lasts <strong>24 hours</strong> from the first message.
                                            </p>
                                        </div>

                                        <!-- Setup & Maintenance Table -->
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="td_inner">Set Up</td>
                                                    <td>USD 85</td>
                                                </tr>
                                                <tr>
                                                    <td class="td_inner">Monthly Maintenance</td>
                                                    <td>USD 50</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- Rate Card Table -->
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Marketing</th>
                                                    <th>Utility</th>
                                                    <th>Authentication</th>
                                                    <th>Service</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="td_inner">Cost per Message - Rest of Africa</td>
                                                    <td>USD 0.032</td>
                                                    <td>USD 0.007</td>
                                                    <td>USD 0.007</td>
                                                    <td>USD 0.002</td>
                                                </tr>
                                                <tr>
                                                    <td class="td_inner">Cost per Message - Nigeria</td>
                                                    <td>USD 0.072</td>
                                                    <td>USD 0.012</td>
                                                    <td>USD 0.012</td>
                                                    <td>USD 0.002</td>
                                                </tr>
                                                <tr>
                                                    <td class="td_inner">Cost per Message - South Africa</td>
                                                    <td>USD 0.052</td>
                                                    <td>USD 0.012</td>
                                                    <td>USD 0.012</td>
                                                    <td>USD 0.002</td>
                                                </tr>
                                                <tr>
                                                    <td class="td_inner">Cost per Message - Egypt</td>
                                                    <td>USD 0.150</td>
                                                    <td>USD 0.009</td>
                                                    <td>USD 0.09</td>
                                                    <td>USD 0.002</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="explanation-section">
                                            <h3>Conversation Categories Explained</h3>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Marketing</strong></td>
                                                        <td class="td_inner">Marketing messages can help businesses achieve a wide range of goals across the funnel, from generating awareness to driving sales to retargeting customers. These include offers, promos, related product suggestions, and abandoned cart reminders.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Utility</strong></td>
                                                        <td class="td_inner">Utility messages are typically triggered by a user action, like placing an order or submitting a payment. These messages are not promotional, and are either specific to or requested by a user (like a package delivery update).</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Authentication</strong></td>
                                                        <td class="td_inner">Authentication messages enable businesses to use one-time passwords (OTPs) for identity verification, potentially at multiple stages of the user journey.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Service</strong></td>
                                                        <td class="td_inner">User-initiated customer support messages. These enable businesses to manage incoming inquiries. Opening a user message opens a 24-hour customer service window which resets with each user message.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="note">
                                            <strong>Note:</strong> Marketing, Utility, and Authentication conversations can only be initiated using approved WhatsApp message templates.
                                        </div>

                                        <div style="margin-top: 25px; color: #666; font-size: 0.85em; font-style: italic;">
                                            <p class="mb-5">
                                                * Prices are Meta standard WhatsApp charges (USD). RebueText platform fees may apply.
                                                * The first 1,000 service conversations per month are free, as per Meta policy.
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--End Tabs-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======== Tabs Wrapper End ======== -->
    @include('frontend.includes.contact-cta')





</div>
@endsection