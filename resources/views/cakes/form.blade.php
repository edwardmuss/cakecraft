@extends('layouts.master')
@section('title') {{ $cake->exists ? 'Edit' : 'New' }} Cake @endsection

@section('content')
@include('includes.flash_message')

@php
    $prices = $cake->exists ? $cake->sizePrices->pluck('price', 'cake_size_id') : collect();
    $selectedFlavours = $cake->exists ? $cake->flavours->keyBy('id') : collect();
    $selectedCollections = $cake->exists ? $cake->collections->pluck('id')->all() : [];
@endphp

<form method="POST" enctype="multipart/form-data"
      action="{{ $cake->exists ? route('cakes.update', $cake->id) : route('cakes.store') }}">
    @csrf
    @if($cake->exists) @method('PUT') @endif

    <div class="cc-page-head">
        <div>
            <h4>{{ $cake->exists ? $cake->name : 'New Cake' }}</h4>
            <div class="sub"><a href="{{ route('cakes.index') }}">&larr; Back to cakes</a></div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cakes.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save Cake</button>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card"><div class="card-body">
                <div class="cc-section-title">Basic details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Cake name *</label>
                        <input name="name" value="{{ old('name', $cake->name) }}" class="form-control" required placeholder="e.g. Black Forest, Jurassic Theme Cake">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type *</label>
                        <select name="product_type" class="form-select">
                            @foreach($types as $k => $label)
                                <option value="{{ $k }}" @selected(old('product_type', $cake->product_type) == $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short description</label>
                        <input name="short_description" value="{{ old('short_description', $cake->short_description) }}" class="form-control" maxlength="200" placeholder="One line shown on cards">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $cake->description) }}</textarea>
                    </div>
                </div>
            </div></div>

            <div class="card"><div class="card-body">
                <div class="cc-section-title">Size &amp; price (KES)</div>
                <p class="text-muted small mb-3">Enter a price only for the sizes you offer. Leave the rest empty. Manage the size list under <a href="{{ route('sizes.index') }}">Sizes</a>.</p>
                @if($sizes->isEmpty())
                    <div class="alert alert-warning mb-0">No sizes yet. <a href="{{ route('sizes.index') }}">Add sizes first</a>.</div>
                @else
                <div class="cc-grid-prices">
                    @foreach($sizes as $size)
                    <div class="cc-price-cell">
                        <label>{{ $size->label }} @if($size->serves)<span class="text-muted">&middot; serves {{ $size->serves }}</span>@endif</label>
                        <input type="number" step="0.01" min="0" name="prices[{{ $size->id }}]" value="{{ old("prices.$size->id", $prices[$size->id] ?? '') }}" class="form-control form-control-sm" placeholder="Price">
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="mt-3" style="max-width:260px">
                    <label class="form-label">Fixed price (if no sizes)</label>
                    <input type="number" step="0.01" min="0" name="fixed_price" value="{{ old('fixed_price', $prices->isEmpty() ? $cake->unit_price : '') }}" class="form-control form-control-sm">
                </div>
            </div></div>

            <div class="card"><div class="card-body">
                <div class="cc-section-title">Flavours offered</div>
                <p class="text-muted small mb-3">Tick the flavours customers can choose on this cake. Add a surcharge if a flavour costs extra. <a href="{{ route('flavours.index') }}">Manage flavours</a>.</p>
                <div class="row g-2">
                    @forelse($flavours as $fl)
                        @php $sel = $selectedFlavours->get($fl->id); @endphp
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 border rounded p-2">
                                <input type="checkbox" class="form-check-input mt-0" name="flavour_ids[]" value="{{ $fl->id }}" id="fl{{ $fl->id }}" @checked($sel)>
                                <label for="fl{{ $fl->id }}" class="flex-grow-1 mb-0">{{ $fl->name }}</label>
                                <input type="number" step="0.01" min="0" name="flavour_extra[{{ $fl->id }}]" value="{{ $sel->pivot->extra_price ?? 0 }}" class="form-control form-control-sm" style="width:90px" title="Extra price">
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted">No flavours yet.</div>
                    @endforelse
                </div>
            </div></div>
        </div>

        <div class="col-lg-4">
            <div class="card"><div class="card-body">
                <div class="cc-section-title">Photo</div>
                <img id="preview" src="{{ $cake->image_url }}" class="cc-img-preview mb-2" alt="">
                <input type="file" name="image" accept="image/*" class="form-control form-control-sm" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
            </div></div>

            <div class="card"><div class="card-body">
                <div class="cc-section-title">Collections</div>
                @forelse($collections as $c)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="collection_ids[]" value="{{ $c->id }}" id="c{{ $c->id }}" @checked(in_array($c->id, old('collection_ids', $selectedCollections)))>
                        <label class="form-check-label" for="c{{ $c->id }}">{{ $c->name }}</label>
                    </div>
                @empty
                    <div class="text-muted small">No collections. <a href="{{ route('collections.index') }}">Create one</a>.</div>
                @endforelse
            </div></div>

            <div class="card"><div class="card-body">
                <div class="cc-section-title">Settings</div>
                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $cake->is_active))><label class="form-check-label" for="act">Active</label></div>
                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_published" value="1" id="pub" @checked(old('is_published', $cake->is_published))><label class="form-check-label" for="pub">Show on website</label></div>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_featured" value="1" id="feat" @checked(old('is_featured', $cake->is_featured))><label class="form-check-label" for="feat">Featured on home page</label></div>
                <label class="form-label">Lead time (days notice)</label>
                <input type="number" min="0" max="60" name="lead_time_days" value="{{ old('lead_time_days', $cake->lead_time_days) }}" class="form-control form-control-sm">
            </div></div>
        </div>
    </div>
</form>
@endsection
