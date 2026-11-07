@extends('storefront.layout')
@section('title', 'All Cakes | ' . \App\Support\Brand::name())

@section('content')
<section class="block"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Shop</div><h1 style="font-size:clamp(30px,4vw,44px)">All cakes</h1></div>
    <div class="pills"><a href="{{ route('store.shop') }}" class="on">All cakes</a>@foreach($collections as $c)<a href="{{ route('store.collection', $c->slug) }}">{{ $c->name }}</a>@endforeach</div>
    <form method="GET" style="max-width:420px;margin:0 auto 30px"><input type="text" name="q" value="{{ $request->q }}" placeholder="Search cakes..." style="width:100%;padding:12px 18px;border:1px solid var(--line);border-radius:999px;font:inherit"></form>
    <div class="products">@forelse($cakes as $cake) @include('storefront.partials.card') @empty <p style="grid-column:1/-1;text-align:center;color:var(--muted)">No cakes found.</p> @endforelse</div>
    <div style="margin-top:30px">{{ $cakes->links() }}</div>
</div></section>
@endsection
