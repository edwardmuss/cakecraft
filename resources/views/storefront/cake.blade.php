@extends('storefront.layout')
@section('title', $cake->name . ' | ' . \App\Support\Brand::name())
@section('description', $cake->short_description ?: \Illuminate\Support\Str::limit(strip_tags($cake->description), 150))

@section('content')
@php
    $sizes = $cake->sizePrices->where('is_active', true)->sortBy(fn($s) => $s->size->sort_order . str_pad((string)($s->size->weight_kg ?? 0), 8, '0', STR_PAD_LEFT))->values();
    $minDate = now()->addDays($cake->lead_time_days)->format('Y-m-d');
@endphp
<div class="wrap"><div class="pgrid">
    <div><img class="main" src="{{ $cake->image_url }}" alt="{{ $cake->name }}"></div>
    <div>
        <div class="eyebrow">@foreach($cake->collections as $c){{ $c->name }}{{ !$loop->last ? ' · ' : '' }}@endforeach</div>
        <h1 style="font-size:clamp(28px,4vw,42px)">{{ $cake->name }}</h1>
        @if($cake->short_description)<p style="color:var(--muted)">{{ $cake->short_description }}</p>@endif

        <form method="POST" action="{{ route('store.order') }}" class="order" id="orderForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $cake->id }}">

            @if($sizes->isNotEmpty())
            <label class="l">Choose a size</label>
            <div class="opt">
                @foreach($sizes as $i => $sp)
                    <label><input type="radio" name="cake_size_id" value="{{ $sp->cake_size_id }}" data-price="{{ $sp->price }}" @checked(old('cake_size_id', $i === 0 ? $sp->cake_size_id : null) == $sp->cake_size_id)><span>{{ $sp->size->label }}@if($sp->size->serves) <small style="color:var(--muted)">· serves {{ $sp->size->serves }}</small>@endif</span></label>
                @endforeach
            </div>
            @error('cake_size_id')<div class="err">{{ $message }}</div>@enderror
            @endif

            @if($cake->flavours->isNotEmpty())
            <label class="l">Choose a flavour</label>
            <div class="opt">
                @foreach($cake->flavours as $i => $fl)
                    <label><input type="radio" name="cake_flavour_id" value="{{ $fl->id }}" data-extra="{{ $fl->pivot->extra_price }}" @checked(old('cake_flavour_id', $i === 0 ? $fl->id : null) == $fl->id)><span>{{ $fl->name }}@if($fl->pivot->extra_price > 0) <small>+{{ number_format($fl->pivot->extra_price) }}</small>@endif</span></label>
                @endforeach
            </div>
            @endif

            <div class="field" style="max-width:120px"><label class="l">Quantity</label><input type="number" name="quantity" id="qty" min="1" max="50" value="{{ old('quantity', 1) }}"></div>
            <div class="total">KES <span id="total">0</span></div>
            <div id="feeNote" style="color:var(--muted);font-size:13px"></div>
            <div style="color:var(--muted);font-size:13px;margin-bottom:18px">Estimated price. We confirm the final amount with you before you pay. Needs {{ $cake->lead_time_days }} day(s) notice.</div>

            <hr style="border:0;border-top:1px solid var(--line);margin:18px 0">

            <div class="two">
                <div class="field"><label class="l">Your name *</label><input type="text" name="name" value="{{ old('name') }}" required>@error('name')<div class="err">{{ $message }}</div>@enderror</div>
                <div class="field"><label class="l">Phone (M-Pesa / WhatsApp) *</label><input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="07xx xxx xxx">@error('phone')<div class="err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="field"><label class="l">Email (optional)</label><input type="email" name="email" value="{{ old('email') }}"></div>
            <div class="two">
                <div class="field"><label class="l">Occasion</label>
                    <select name="occasion_type"><option value="">Select...</option>@foreach($occasionTypes as $k => $l)<option value="{{ $k }}" @selected(old('occasion_type')==$k)>{{ $l }}</option>@endforeach</select></div>
                <div class="field"><label class="l">Who is it for?</label><input type="text" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="e.g. Brian"></div>
            </div>
            <div class="two">
                <div class="field"><label class="l">Date needed *</label><input type="date" name="needed_on" value="{{ old('needed_on') }}" min="{{ $minDate }}" required>@error('needed_on')<div class="err">{{ $message }}</div>@enderror</div>
                <div class="field"><label class="l">Pickup or delivery *</label>
                    <select name="fulfilment_type" id="ful"><option value="pickup" @selected(old('fulfilment_type')=='pickup')>Pickup</option><option value="delivery" @selected(old('fulfilment_type')=='delivery')>Delivery</option></select></div>
            </div>
            @if($zones->isNotEmpty())
            <div class="field" id="zoneWrap" style="display:none"><label class="l">Delivery area *</label>
                <select name="delivery_zone_id" id="zone"><option value="" data-fee="0">Select your area...</option>@foreach($zones as $z)<option value="{{ $z->id }}" data-fee="{{ $z->fee }}" @selected(old('delivery_zone_id')==$z->id)>{{ $z->name }} (+KES {{ number_format($z->fee) }})</option>@endforeach</select>
                @error('delivery_zone_id')<div class="err">{{ $message }}</div>@enderror</div>
            @endif
            <div class="field" id="addr" style="display:none"><label class="l">Delivery address *</label><textarea name="delivery_address" rows="2">{{ old('delivery_address') }}</textarea>@error('delivery_address')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label class="l">Message on the cake</label><input type="text" name="cake_message" maxlength="120" value="{{ old('cake_message') }}" placeholder="Happy Birthday Brian!"></div>
            <div class="field"><label class="l">Anything else we should know?</label><textarea name="notes" rows="2" placeholder="Theme, colours, allergies...">{{ old('notes') }}</textarea></div>

            <button class="btn" style="width:100%;padding:15px">Send cake request</button>
            <p style="text-align:center;color:var(--muted);font-size:12px;margin:10px 0 0">No payment now. We will contact you to confirm.</p>
        </form>
    </div>
</div>

@if($cake->description)
<div style="max-width:760px;margin:0 auto 40px"><h2 style="font-size:26px;margin-bottom:10px">About this cake</h2><div style="color:#4a3f43;white-space:pre-line">{{ $cake->description }}</div></div>
@endif

@if($related->isNotEmpty())
<section class="block" style="padding-top:20px"><div class="section-head"><h2>You may also like</h2></div>
<div class="products">@foreach($related as $rel) @include('storefront.partials.card', ['cake' => $rel]) @endforeach</div></section>
@endif
</div>
@endsection

@push('scripts')
<script>
(function(){
    const fmt = n => Math.round(n).toLocaleString('en-KE');
    const form = document.getElementById('orderForm');
    function calc(){
        const size = form.querySelector('input[name=cake_size_id]:checked');
        const fl = form.querySelector('input[name=cake_flavour_id]:checked');
        const qty = Math.max(1, parseInt(document.getElementById('qty').value) || 1);
        let price = size ? parseFloat(size.dataset.price) : {{ (float) $cake->unit_price }};
        if (fl) price += parseFloat(fl.dataset.extra || 0);
        const zoneSel = document.getElementById('zone');
        const fee = (zoneSel && document.getElementById('ful').value === 'delivery') ? (parseFloat(zoneSel.selectedOptions[0].dataset.fee) || 0) : 0;
        document.getElementById('total').textContent = fmt(price * qty + fee);
        const note = document.getElementById('feeNote');
        if (note) note.textContent = fee > 0 ? 'Includes KES ' + fmt(fee) + ' delivery.' : '';
    }
    form.addEventListener('change', calc); form.addEventListener('input', calc); calc();
    const ful = document.getElementById('ful'), addr = document.getElementById('addr');
    const zoneWrap = document.getElementById('zoneWrap');
    const toggle = () => { addr.style.display = ful.value === 'delivery' ? 'block' : 'none'; if (zoneWrap) zoneWrap.style.display = ful.value === 'delivery' ? 'block' : 'none'; calc(); };
    ful.addEventListener('change', toggle); toggle();
})();
</script>
@endpush
