@extends('storefront.layout')
@section('title', 'Request received | ' . \App\Support\Brand::name())

@section('content')
<section class="block"><div class="wrap" style="max-width:640px;text-align:center">
    <div class="eyebrow">Thank you</div>
    <h1 style="font-size:38px">We have your cake request!</h1>
    <p style="color:var(--muted)">Your reference is <b style="color:var(--rose-dark)">{{ $order->reference }}</b>. We will call or WhatsApp {{ $order->phone }} shortly to confirm the details and the final price.</p>
    <div class="card" style="text-align:left;margin:26px 0"><div class="body" style="padding:20px">
        <h3>{{ $order->cake_name }} &times; {{ $order->quantity }}</h3>
        <p style="color:var(--muted);margin:6px 0">{{ collect([$order->size_label, $order->flavour_name])->filter()->implode(' · ') }}</p>
        <p style="margin:6px 0">Needed on <b>{{ $order->needed_on->format('l, j F Y') }}</b> ({{ $order->fulfilment_type }})</p>
        @if($order->delivery_fee > 0)<p style="margin:6px 0;color:var(--muted)">Delivery to {{ $order->delivery_zone_name }}: KES {{ number_format($order->delivery_fee) }}</p>@endif
        <p class="price" style="margin:6px 0">Estimated: KES {{ number_format($order->estimated_price + $order->delivery_fee) }}</p>
    </div></div>
    <a href="{{ route('store.shop') }}" class="btn">Keep browsing</a>
    <a href="https://wa.me/{{ \App\Support\Brand::get('whatsapp') }}?text={{ urlencode('Hi, my cake request is ' . $order->reference) }}" target="_blank" class="btn ghost">Chat on WhatsApp</a>
</div></section>
@endsection
