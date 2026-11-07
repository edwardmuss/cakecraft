@extends('storefront.layout')
@section('title', 'Price List | ' . \App\Support\Brand::name())

@section('content')
<section class="block"><div class="wrap">
    <div class="section-head"><div class="eyebrow">Full menu</div><h1 style="font-size:clamp(30px,4vw,44px)">Every flavour. Every price.</h1><p>Tap a flavour to order.</p></div>
    <div style="overflow-x:auto">
    <table class="menu-table">
        <thead><tr><th>Flavour</th>@foreach($sizes as $s)<th>{{ $s->label }}</th>@endforeach</tr></thead>
        <tbody>
        @foreach($cakes as $cake)
            @php $map = $cake->sizePrices->pluck('price', 'cake_size_id'); @endphp
            <tr>
                <td><a href="{{ route('store.cake', $cake->slug) }}">{{ $cake->name }}</a></td>
                @foreach($sizes as $s)<td>{{ isset($map[$s->id]) ? 'KES ' . number_format($map[$s->id]) : '-' }}</td>@endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div></section>
@endsection
