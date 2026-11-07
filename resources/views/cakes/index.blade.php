@extends('layouts.master')
@section('title') Cakes @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div>
        <h4>Cakes</h4>
        <div class="sub">Everything shown on your website is managed here.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('collections.index') }}" class="btn btn-light btn-sm"><i class="ri-folders-line me-1"></i> Collections</a>
        <a href="{{ route('flavours.index') }}" class="btn btn-light btn-sm"><i class="ri-cake-3-line me-1"></i> Flavours</a>
        <a href="{{ route('sizes.index') }}" class="btn btn-light btn-sm"><i class="ri-ruler-line me-1"></i> Sizes</a>
        @can('create products')
        <a href="{{ route('cakes.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line me-1"></i> New Cake</a>
        @endcan
    </div>
</div>

<div class="card">
    <form method="GET" class="cc-toolbar">
        <input type="text" name="q" value="{{ $request->q }}" class="form-control" placeholder="Search cakes...">
        <select name="collection" class="form-select">
            <option value="">All collections</option>
            @foreach($collections as $c)
                <option value="{{ $c->id }}" @selected($request->collection == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="type" class="form-select">
            <option value="">All types</option>
            @foreach($types as $k => $label)
                <option value="{{ $k }}" @selected($request->type == $k)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select">
            <option value="">Any status</option>
            <option value="active" @selected($request->status == 'active')>Active</option>
            <option value="inactive" @selected($request->status == 'inactive')>Inactive</option>
        </select>
        <button class="btn btn-soft-primary btn-sm">Filter</button>
        @if($request->hasAny(['q','collection','type','status']))
            <a href="{{ route('cakes.index') }}" class="btn btn-link btn-sm">Clear</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead>
            <tr>
                <th style="width:60px"></th>
                <th>Cake</th>
                <th>Collections</th>
                <th>Type</th>
                <th>Sizes</th>
                <th class="text-end">From (KES)</th>
                <th>Website</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($cakes as $cake)
                <tr>
                    <td><img src="{{ $cake->image_url }}" class="cc-thumb" alt=""></td>
                    <td>
                        <div class="cc-name"><a href="{{ route('cakes.edit', $cake->id) }}">{{ $cake->name }}</a></div>
                        @if(!$cake->is_active)<span class="cc-status bad">Inactive</span>@endif
                    </td>
                    <td>@foreach($cake->collections as $c)<span class="cc-chip">{{ $c->name }}</span>@endforeach</td>
                    <td>{{ \App\Models\Product::TYPES[$cake->product_type] ?? $cake->product_type }}</td>
                    <td>{{ $cake->sizePrices->count() }}</td>
                    <td class="text-end">{{ number_format($cake->min_price) }}</td>
                    <td>
                        <form method="POST" action="{{ route('cakes.toggle', $cake->id) }}">@csrf
                            <button class="btn btn-link p-0 border-0 cc-status {{ $cake->is_published ? 'ok' : '' }}">{{ $cake->is_published ? 'Visible' : 'Hidden' }}</button>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('cakes.edit', $cake->id) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                        @can('delete products')
                        <form method="POST" action="{{ route('cakes.destroy', $cake->id) }}" class="d-inline" onsubmit="return confirm('Delete this cake?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-soft-danger">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No cakes yet. <a href="{{ route('cakes.create') }}">Add your first cake</a>.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $cakes->links() }}</div>
</div>
@endsection
