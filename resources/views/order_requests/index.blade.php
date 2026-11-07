@extends('layouts.master')
@section('title') Web Orders @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Web Orders</h4><div class="sub">Cake requests placed on your website. Confirm with the customer, then convert to an invoice.</div></div>
</div>

<div class="cc-kpis">
    <div class="cc-kpi accent"><div class="lbl">New</div><div class="val">{{ $counts['new'] ?? 0 }}</div></div>
    <div class="cc-kpi"><div class="lbl">Contacted</div><div class="val">{{ $counts['contacted'] ?? 0 }}</div></div>
    <div class="cc-kpi"><div class="lbl">Converted</div><div class="val">{{ $counts['converted'] ?? 0 }}</div></div>
    <div class="cc-kpi"><div class="lbl">Declined</div><div class="val">{{ $counts['declined'] ?? 0 }}</div></div>
</div>

<div class="card">
    <form method="GET" class="cc-toolbar">
        <input name="q" value="{{ $request->q }}" class="form-control" placeholder="Name, phone or reference">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            @foreach(['new','contacted','converted','declined'] as $s)<option value="{{ $s }}" @selected($request->status==$s)>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <button class="btn btn-soft-primary btn-sm">Filter</button>
    </form>
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th>Ref</th><th>Customer</th><th>Cake</th><th>Needed on</th><th>Occasion</th><th class="text-end">Est. (KES)</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($requests as $r)
                @php $st = ['new'=>'warn','contacted'=>'info','converted'=>'ok','declined'=>'bad'][$r->status] ?? ''; @endphp
                <tr>
                    <td class="cc-name">{{ $r->reference }}<div class="text-muted small">{{ $r->created_at->diffForHumans() }}</div></td>
                    <td>{{ $r->name }}<div class="text-muted small"><a href="tel:{{ $r->phone }}">{{ $r->phone }}</a></div></td>
                    <td>
                        {{ $r->cake_name }} &times; {{ $r->quantity }}
                        <div class="text-muted small">{{ collect([$r->size_label, $r->flavour_name])->filter()->implode(' · ') }}</div>
                        @if($r->cake_message)<div class="small fst-italic">"{{ $r->cake_message }}"</div>@endif
                    </td>
                    <td>{{ $r->needed_on?->format('D, j M Y') }}<div class="text-muted small">{{ ucfirst($r->fulfilment_type) }}@if($r->delivery_address): {{ \Illuminate\Support\Str::limit($r->delivery_address, 30) }}@endif</div></td>
                    <td>{{ $r->occasion_type ? ucfirst(str_replace('_',' ',$r->occasion_type)) : '-' }}@if($r->recipient_name)<div class="text-muted small">for {{ $r->recipient_name }}</div>@endif</td>
                    <td class="text-end">{{ number_format($r->estimated_price + $r->delivery_fee) }}@if($r->delivery_fee > 0)<div class="text-muted small">incl. {{ number_format($r->delivery_fee) }} delivery</div>@endif</td>
                    <td><span class="cc-status {{ $st }}">{{ ucfirst($r->status) }}</span></td>
                    <td class="text-end text-nowrap">
                        @if($r->invoice_id)
                            <a href="{{ route('invoices.show', $r->invoice_id) }}" class="btn btn-sm btn-soft-primary">View invoice</a>
                        @elseif($r->status !== 'declined')
                            <a href="https://wa.me/{{ preg_replace('/^0/', '254', preg_replace('/\D/', '', $r->phone)) }}?text={{ urlencode("Hi {$r->name}, thanks for your cake request {$r->reference} ({$r->cake_name}). ") }}" target="_blank" class="btn btn-sm btn-soft-success"><i class="ri-whatsapp-line"></i></a>
                            <form method="POST" action="{{ route('order-requests.status', $r->id) }}" class="d-inline">@csrf @method('PUT')<input type="hidden" name="status" value="contacted"><button class="btn btn-sm btn-light">Mark contacted</button></form>
                            <form method="POST" action="{{ route('order-requests.convert', $r->id) }}" class="d-inline">@csrf<button class="btn btn-sm btn-primary">Create invoice</button></form>
                            <form method="POST" action="{{ route('order-requests.status', $r->id) }}" class="d-inline" onsubmit="return confirm('Decline this request?')">@csrf @method('PUT')<input type="hidden" name="status" value="declined"><button class="btn btn-sm btn-soft-danger">Decline</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No web orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $requests->links() }}</div>
</div>
@endsection
