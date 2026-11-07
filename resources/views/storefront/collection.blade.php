@extends('storefront.layout')
@section('title', $collection->name . ' | ' . \App\Support\Brand::name())

@section('content')
<section class="block"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Collection</div><h1 style="font-size:clamp(30px,4vw,44px)">{{ $collection->name }}</h1>@if($collection->description)<p>{{ $collection->description }}</p>@endif</div>
    <div class="pills"><a href="{{ route('store.shop') }}">All cakes</a>@foreach($collections as $c)<a href="{{ route('store.collection', $c->slug) }}" class="{{ $c->id == $collection->id ? 'on' : '' }}">{{ $c->name }}</a>@endforeach</div>
    <div class="products">@forelse($cakes as $cake) @include('storefront.partials.card') @empty <p style="grid-column:1/-1;text-align:center;color:var(--muted)">No cakes in this collection yet.</p> @endforelse</div>
    <div style="margin-top:30px">{{ $cakes->links() }}</div>
</div></section>
@endsection
