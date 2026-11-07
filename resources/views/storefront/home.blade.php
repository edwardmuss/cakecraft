@extends('storefront.layout')
@section('title', \App\Support\Brand::name() . ' | Custom Cakes')

@section('content')
<section class="hero">
    <div class="wrap grid">
        <div>
            <div class="eyebrow">{{ \App\Support\Brand::name() }}</div>
            <h1>{{ \App\Support\Brand::copy('hero_headline') }}</h1>
            <p>{{ \App\Support\Brand::copy('hero_text') }}</p>
            <p style="margin-top:24px"><a href="{{ route('store.shop') }}" class="btn">Browse cakes</a> &nbsp; <a href="{{ route('store.menu') }}" class="btn ghost">See price list</a></p>
        </div>
        <img src="{{ ($featured->first() ?? $latest->first())?->image_url ?? asset('images/cake-placeholder.svg') }}" alt="">
    </div>
</section>

<div class="steps"><div class="wrap row">
    @foreach([1,2,3,4] as $n)<div><b>{{ $n }}</b>{{ \App\Support\Brand::copy('step_'.$n) }}</div>@endforeach
</div></div>

@if($collections->isNotEmpty())
<section class="block"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Shop by occasion</div><h2>Find the perfect cake</h2></div>
    <div class="cols">
        @foreach($collections->take(8) as $c)
            <a href="{{ route('store.collection', $c->slug) }}" class="collection"><img src="{{ $c->image_url }}" alt="" loading="lazy"><span>{{ $c->name }}<small>{{ $c->products_count }} cakes</small></span></a>
        @endforeach
    </div>
</div></section>
@endif

@if($featured->isNotEmpty())
<section class="block" style="background:#fff"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Featured</div><h2>Our favourites</h2></div>
    <div class="products">@foreach($featured as $cake) @include('storefront.partials.card') @endforeach</div>
</div></section>
@endif

@if($signature->isNotEmpty())
<section class="block"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Signature flavours</div><h2>Pick a flavour, tap a size</h2><p>Classic recipes, baked fresh to order.</p></div>
    <div class="products">@foreach($signature as $cake) @include('storefront.partials.card') @endforeach</div>
    <p style="text-align:center;margin-top:28px"><a href="{{ route('store.menu') }}" class="btn ghost">See the full price list</a></p>
</div></section>
@endif

<section class="block" style="background:#fff"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Latest</div><h2>Fresh from our kitchen</h2></div>
    <div class="products">@foreach($latest as $cake) @include('storefront.partials.card') @endforeach</div>
    @if($latest->isEmpty())<p style="text-align:center;color:var(--muted)">Our cakes are coming soon.</p>@endif
</div></section>
@endsection
