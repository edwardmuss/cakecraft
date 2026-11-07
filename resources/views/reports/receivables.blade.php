@extends('layouts.master')
@section('title') Receivables @endsection

@section('script')
<script>
const BUSINESS = {{ Js::from(\App\Services\OccasionService::setting(Auth::user()->branch_id, 'business_name', \App\Support\Brand::name())) }};
function remindCustomer(c) {
    const first = c.name.split(' ')[0];
    const text = `Hi ${first}, a friendly reminder that you have an outstanding balance of KES ${c.balance} (${c.invoices}) with ${BUSINESS}. Kindly settle at your earliest convenience. Thank you.`;
    Swal.fire({
        title: 'SMS ' + c.name,
        input: 'textarea',
        inputValue: text,
        inputAttributes: { maxlength: 480, rows: 6 },
        showCancelButton: true,
        confirmButtonText: 'Send SMS',
        showLoaderOnConfirm: true,
        preConfirm: msg => axios.post(`{{ url('reports/receivables') }}/${c.id}/remind`, { message: msg })
            .then(r => r.data)
            .catch(e => Swal.showValidationMessage(e.response?.data?.message || 'Could not send the SMS.'))
    }).then(res => { if (res.isConfirmed && res.value) Swal.fire('Sent', res.value.success, 'success'); });
}
</script>
@endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Receivables</h4><div class="sub">Unpaid amounts owed by each customer (cancelled invoices excluded).</div></div>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-soft-primary btn-sm"><i class="ri-download-2-line me-1"></i> Export CSV</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card mb-0"><div class="card-body"><div class="text-muted small">Total outstanding</div><div class="fs-4 fw-semibold">KES {{ number_format($totals->balance, 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card mb-0"><div class="card-body"><div class="text-muted small">Overdue</div><div class="fs-4 fw-semibold text-danger">KES {{ number_format($totals->overdue, 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card mb-0"><div class="card-body"><div class="text-muted small">Customers owing</div><div class="fs-4 fw-semibold">{{ $totals->customers }}</div></div></div></div>
    <div class="col-md-3"><div class="card mb-0"><div class="card-body"><div class="text-muted small">Unpaid invoices</div><div class="fs-4 fw-semibold">{{ $totals->invoices }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search customer name or phone..."></div>
            <div class="col-auto"><button class="btn btn-primary">Search</button> @if($search)<a href="{{ route('reports.receivables') }}" class="btn btn-light">Clear</a>@endif</div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table cc-table align-middle mb-0">
            <thead><tr><th>Customer</th><th>Phone</th><th class="text-center">Open invoices</th><th class="text-end">Billed</th><th class="text-end">Paid</th><th class="text-end">Outstanding</th><th class="text-end">Current</th><th class="text-end">1-30</th><th class="text-end">31-60</th><th class="text-end">61-90</th><th class="text-end">90+</th><th></th></tr></thead>
            <tbody>
            @forelse($rows as $r)
                <tr data-bs-toggle="collapse" data-bs-target="#cust{{ $r->customer_id }}" style="cursor:pointer">
                    <td class="cc-name"><i class="ri-arrow-right-s-line"></i> {{ $r->name }}</td>
                    <td>{{ $r->phone ?: '-' }}</td>
                    <td class="text-center">{{ $r->count }}</td>
                    <td class="text-end">{{ number_format($r->billed, 2) }}</td>
                    <td class="text-end">{{ number_format($r->paid, 2) }}</td>
                    <td class="text-end fw-semibold">{{ number_format($r->balance, 2) }}</td>
                    <td class="text-end {{ $r->age['current'] > 0 ? '' : 'text-muted' }}">{{ $r->age['current'] > 0 ? number_format($r->age['current'], 2) : '-' }}</td>
                    <td class="text-end {{ $r->age['d30'] > 0 ? 'text-warning' : 'text-muted' }}">{{ $r->age['d30'] > 0 ? number_format($r->age['d30'], 2) : '-' }}</td>
                    <td class="text-end {{ $r->age['d60'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $r->age['d60'] > 0 ? number_format($r->age['d60'], 2) : '-' }}</td>
                    <td class="text-end {{ $r->age['d90'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $r->age['d90'] > 0 ? number_format($r->age['d90'], 2) : '-' }}</td>
                    <td class="text-end {{ $r->age['d90p'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $r->age['d90p'] > 0 ? number_format($r->age['d90p'], 2) : '-' }}</td>
                    <td class="text-end text-nowrap">
                        @if($r->phone)
                        <button type="button" class="btn btn-sm btn-soft-primary" onclick="event.stopPropagation(); remindCustomer({{ Js::from(['id' => $r->customer_id, 'name' => $r->name, 'balance' => number_format($r->balance, 2), 'invoices' => $r->invoices->pluck('number')->implode(', ')]) }})"><i class="ri-message-2-line"></i> SMS</button>
                        @endif
                    </td>
                </tr>
                <tr class="collapse" id="cust{{ $r->customer_id }}">
                    <td colspan="11" class="bg-light">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Invoice</th><th>Date</th><th>Due</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Balance</th><th></th></tr></thead>
                            <tbody>
                            @foreach($r->invoices as $i)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $i->id) }}">{{ $i->number }}</a></td>
                                    <td>{{ optional($i->date)->format('d M Y') }}</td>
                                    <td>{{ $i->due_date ? $i->due_date->format('d M Y') : '-' }}</td>
                                    <td class="text-end">{{ number_format($i->total, 2) }}</td>
                                    <td class="text-end">{{ number_format($i->paid, 2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($i->balance, 2) }}</td>
                                    <td class="text-end">@if($i->overdue_days)<span class="cc-status bad">{{ $i->overdue_days }} days overdue</span>@endif</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center text-muted py-5">Nothing outstanding. Every invoice is paid.</td></tr>
            @endforelse
            </tbody>
            @if($rows->count())
            <tfoot><tr class="fw-semibold"><td colspan="3">Total</td><td class="text-end">{{ number_format($rows->sum('billed'), 2) }}</td><td class="text-end">{{ number_format($rows->sum('paid'), 2) }}</td><td class="text-end">{{ number_format($totals->balance, 2) }}</td><td class="text-end">{{ number_format($totals->age['current'], 2) }}</td><td class="text-end">{{ number_format($totals->age['d30'], 2) }}</td><td class="text-end">{{ number_format($totals->age['d60'], 2) }}</td><td class="text-end">{{ number_format($totals->age['d90'], 2) }}</td><td class="text-end">{{ number_format($totals->age['d90p'], 2) }}</td><td></td></tr></tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
