@extends('frontend.layouts.app')

@section('title', 'WhatsApp Business Api Pricing | RebueText - High-Volume Messaging for Businesses')
@section('keywords', "Reach thousands instantly with RebueText's WhatsApp Business Api. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', 'Reach thousands instantly with RebueText\'s WhatsApp Business Api .RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/whatsapp.jpg'))

@section('content')
<link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

<div class="site-wrapper-reveal">
    <!--========= About Get Started Wrapper Start ===========-->
    <div class="processing-contact-us-bg bg-secondary section-space--ptb_120">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="resources-left-box">
                        <div class="resources-inner">
                            {{-- <h6 class="sub-title mb-20 text-primary-light">Get Started</h6> --}}
                            <h1 class="heading text-white">Pay-as-you go pricing for the<span class="text-color-primary"> WhatsApp Business API.</span></h1>
                            <p class="text-secondary-light">Our transparent, scalable WhatsApp pricing makes it easy to understand how much you’ll pay to send WhatsApp messages from your business.</p>
                            <div class="button mt-30">
                                @auth
                                    <a href="{{ route('root') }}" class="ht-btn btn--gradient ht-btn-md">Go to Dashboard</a>
                                @else
                                    <a href="{{ route('register') }}" target="_blank" class="ht-btn btn--gradient ht-btn-md">Sign Up Now</a>
                                @endauth
                                <a href="{{ route('products.whatsapp') }}" class="ht-btn btn--white ht-btn-md">Features</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="resources-right-box">
                        <div class="resources-right-inner text-center">
                            <div class="resources-images">
                                <img class="img-fluid" src="{{ asset('frontend/assets/images/banners/whatsapp-header-imgx500.webp') }}" alt="Whatsapp Pricing">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--========= About Get Started Wrapper End ===========-->
    <!--======== Tabs Wrapper Start ======== -->
    <div class="tabs-wrapper  section-space--ptb_10">
        <div class="container">
            <div class="row g-3">

            <div class="col-12">
                <div class="note">
                    <div class="fw-semibold mb-1">Disclaimer</div>
                    <div class="small">
                        Meta WhatsApp rates vary by destination market and category. The prices shown are estimates.
                        If an account has custom pricing/agreements, final billing may differ.
                        WhatsApp pricing is calculated based on RebueText's per-message fee, plus Meta’s per-template message fee, which RebueText passes through to customers. RebueText's per-message fee for WhatsApp is ${{ number_format($platformFeeUsd, 3) }}, outbound. Meta’s per-template message fees are shown below.
                    </div>
                    <div class="small mt-2">
                        <span class="badge bg-secondary">Markup: {{ (int)($markup*100) }}%</span>
                        <span class="badge bg-secondary">Platform fee: ${{ number_format($platformFeeUsd, 3) }}</span>
                        <span class="badge bg-secondary">FX: 1 USD = {{ number_format($fx, 4) }} KES</span>
                    </div>
                </div>
            </div>

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
                    {{-- * Prices are Meta standard WhatsApp charges (USD). RebueText platform fees may apply. --}}
                    {{-- * The first 1,000 service conversations per month are free, as per Meta policy. --}}
                </p>
            </div>

            {{-- Calculator --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Quick Calculator</h4>
                        <small class="text-muted">Detects market by phone prefix</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Destination phone (MSISDN)</label>
                                <input type="text" class="form-control" id="calcPhone" placeholder="e.g. 254712345678">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select class="form-control" id="calcCategory">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" id="calcBtn">Calculate</button>
                            </div>
                        </div>

                        <div class="mt-3 d-none note" id="calcResult">
                            <div class="border rounded p-3 bg-light">
                                <div class="fw-semibold mb-1" id="calcMarket"></div>
                                <div class="small text-muted mb-2" id="calcAsOf"></div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="fw-semibold">Meta</div>
                                        <div id="calcMeta"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fw-semibold">Our Price</div>
                                        <div id="calcOur"></div>
                                    </div>
                                </div>

                                <div class="small text-muted mt-2" id="calcComponents"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Rate Table --}}
            <div class="col-12">
                <div class="card-2">
                    <div class="card-header-2 d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Rate Card (Meta vs Our Price)</h4>
                        <form class="d-flex gap-2" method="GET" action="{{ route('pricing.whatsapp-api') }}">
                            <select name="type" class="form-control">
                                <option value="">All</option>
                                <option value="country" @selected($filters['type']==='country')>Country</option>
                                <option value="region" @selected($filters['type']==='region')>Region</option>
                                <option value="other" @selected($filters['type']==='other')>Other</option>
                            </select>
                            <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Search market...">
                            <button class="btn btn-outline-primary" type="submit">Filter</button>
                        </form>
                    </div>

                    <div class="card-body-2 table-responsive mb-3">
                        <table class="">
                            <thead class="table-light">
                                <tr>
                                    <th style="">Market</th>
                                    <th>Type</th>
                                    @foreach($categories as $catKey => $catLabel)
                                        <th style="">{{ $catLabel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr>
                                        <td class="fw-semibold td_inner">{{ $row['name'] }}</td>
                                        <td><span class="badge bg-secondary td_inner">{{ $row['type'] }}</span></td>

                                        @foreach($categories as $catKey => $catLabel)
                                            @php
                                                $cell = $row['rates'][$catKey];
                                            @endphp
                                            <td class="td_inner">
                                                <div class="small text-muted">Meta</div>
                                                <div>${{ number_format($cell['meta_usd'], 6) }}</div>
                                                <div class="small text-muted">KES {{ number_format($cell['meta_kes'], 4) }}</div>

                                                <hr class="my-2">

                                                <div class="small text-muted">Our price</div>
                                                <div class="fw-semibold">${{ number_format($cell['our_usd'], 6) }}</div>
                                                <div class="small text-muted">KES {{ number_format($cell['our_kes'], 4) }}</div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ 2 + count($categories) }}">No markets found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <!--======== Tabs Wrapper End ======== -->
    @include('frontend.includes.contact-cta')
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
document.getElementById('calcBtn').addEventListener('click', async function () {
    const phone = document.getElementById('calcPhone').value.trim();
    const category = document.getElementById('calcCategory').value;

    if (!phone){
        Swal.fire({
            icon: 'success',
            title: 'Error',
            html: "Phone number prefix is required",
            confirmButtonText: 'OK'
        });
        // alert('Phone number prefix is required');
        return;
    } 

    try {
        const res = await axios.post("{{ route('whatsapp.pricing.calc') }}", {
            phone, category
        }, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });

        const d = res.data.data;

        document.getElementById('calcMarket').innerText = `Market: ${d.market} • Category: ${d.category}`;
        document.getElementById('calcAsOf').innerText = `As of: ${d.as_of} • FX: 1 USD = ${d.fx_usd_kes} KES`;

        document.getElementById('calcMeta').innerText = `$${d.meta.usd} (KES ${d.meta.kes})`;
        document.getElementById('calcOur').innerText  = `$${d.our_price.usd} (KES ${d.our_price.kes})`;
        document.getElementById('calcComponents').innerText =
            `Platform fee: $${d.components.platform_fee_usd} • Markup: ${d.components.markup_percent}%`;

        document.getElementById('calcResult').classList.remove('d-none');
    } catch (e) {
        console.error(e);
        Swal.fire({
            icon: 'success',
            title: 'Error',
            html: "Failed to calculate. Check phone format or category.",
            confirmButtonText: 'OK'
        });
        // alert('Failed to calculate. Check phone format or category.');
    }
});
</script>
@endsection