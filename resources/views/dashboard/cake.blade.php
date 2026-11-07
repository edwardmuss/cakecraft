@extends('layouts.master')
@section('title') Dashboard @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', Auth::user()->name)[0] }}</h4>
        <div class="sub">{{ now()->format('l, j F Y') }}</div></div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line me-1"></i> New Invoice</a>
    </div>
</div>

<div class="cc-kpis">
    <div class="cc-kpi accent"><div class="lbl">Sales this month</div><div class="val">KES {{ number_format($kpi['sales_month']) }}</div></div>
    <div class="cc-kpi"><div class="lbl">Received this month</div><div class="val">KES {{ number_format($kpi['received_month']) }}</div></div>
    <div class="cc-kpi"><div class="lbl">Receivables</div><div class="val">KES {{ number_format($kpi['receivables']) }}</div><div class="small text-muted">{{ $kpi['overdue_count'] }} overdue</div></div>
    <div class="cc-kpi"><div class="lbl">New web orders</div><div class="val">{{ $kpi['new_web_orders'] }}</div><a href="{{ route('order-requests.index', ['status' => 'new']) }}" class="small">Review &rarr;</a></div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between"><h6 class="mb-0">Cakes to prepare - next 7 days</h6><a href="{{ route('invoices.index') }}" class="small">All invoices</a></div>
            <div class="table-responsive">
                <table class="table cc-table">
                    <thead><tr><th>Needed</th><th>Customer</th><th>Cakes</th><th>Fulfilment</th><th>Balance</th></tr></thead>
                    <tbody>
                    @forelse($dueSoon as $inv)
                        @php $d = \Carbon\Carbon::parse($inv->occasion_date); $bal = $inv->total_amount - $inv->amount_paid; @endphp
                        <tr>
                            <td><div class="cc-day-pill {{ $d->isToday() ? 'today' : ($d->diffInDays(today()) <= 2 ? 'soon' : '') }}">{{ $d->format('j') }}<small>{{ $d->isToday() ? 'Today' : $d->format('D M') }}</small></div></td>
                            <td><a href="{{ route('invoices.show', $inv->id) }}" class="cc-name">{{ $inv->customer->name }}</a><div class="text-muted small">{{ $inv->invoice_number }}{{ $inv->recipient_name ? ' · for ' . $inv->recipient_name : '' }}</div></td>
                            <td>@foreach($inv->items as $it)<div>{{ $it->item_name }} @if($it->size_label)<span class="cc-chip">{{ $it->size_label }}</span>@endif @if($it->flavour_name)<span class="cc-chip">{{ $it->flavour_name }}</span>@endif</div>@if($it->cake_message)<div class="small fst-italic text-muted">"{{ $it->cake_message }}"</div>@endif @endforeach</td>
                            <td>{{ ucfirst($inv->fulfilment_type ?? 'pickup') }}</td>
                            <td>@if($bal > 0)<span class="cc-status warn">KES {{ number_format($bal) }}</span>@else<span class="cc-status ok">Paid</span>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No cakes due in the next 7 days.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Sales vs money received</h6></div>
            <div class="card-body"><canvas id="salesChart" height="110"></canvas></div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between"><h6 class="mb-0">Birthdays &amp; anniversaries coming</h6><a href="{{ route('occasions.index') }}" class="small">Open</a></div>
            <div class="list-group list-group-flush">
                @forelse($occasions as $o)
                    <div class="list-group-item d-flex align-items-center gap-3">
                        <div class="cc-day-pill {{ $o->days_away <= 7 ? 'soon' : '' }}">{{ $o->days_away }}<small>days</small></div>
                        <div class="flex-grow-1"><div class="cc-name">{{ $o->customer->name }}</div><div class="small text-muted">{{ ucfirst($o->occasion_type) }}{{ $o->recipient_name ? ' · ' . $o->recipient_name : '' }} · {{ $o->next_date->format('j M') }}</div></div>
                    </div>
                @empty
                    <div class="list-group-item text-muted text-center py-4">Nothing to remind in the next 14 days.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between"><h6 class="mb-0">New web orders</h6><a href="{{ route('order-requests.index') }}" class="small">All</a></div>
            <div class="list-group list-group-flush">
                @forelse($webOrders as $w)
                    <a href="{{ route('order-requests.index') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between"><span class="cc-name">{{ $w->name }}</span><span class="small text-muted">{{ $w->created_at->diffForHumans(null, true) }}</span></div>
                        <div class="small text-muted">{{ $w->cake_name }} {{ $w->size_label ? '· ' . $w->size_label : '' }} · needed {{ $w->needed_on?->format('j M') }}</div>
                    </a>
                @empty
                    <div class="list-group-item text-muted text-center py-4">No new web orders.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Overdue invoices</h6></div>
            <div class="list-group list-group-flush">
                @forelse($overdue as $o)
                    <a href="{{ route('invoices.show', $o->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>{{ $o->customer->name }}<div class="small text-muted">{{ $o->invoice_number }} · due {{ \Carbon\Carbon::parse($o->due_date)->format('j M') }}</div></span>
                        <span class="cc-status bad">KES {{ number_format($o->total_amount - $o->amount_paid) }}</span>
                    </a>
                @empty
                    <div class="list-group-item text-muted text-center py-4">Nothing overdue.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Top cakes this year</h6></div>
            <div class="list-group list-group-flush">
                @forelse($topCakes as $t)
                    <div class="list-group-item d-flex justify-content-between"><span>{{ $t->item_name }} <span class="text-muted small">&times;{{ $t->qty }}</span></span><b>{{ number_format($t->revenue) }}</b></div>
                @empty
                    <div class="list-group-item text-muted text-center py-4">No sales yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const C = @json($chart);
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: { labels: C.labels, datasets: [
        { label: 'Sales', data: C.sales, backgroundColor: '#b76e79', borderRadius: 4 },
        { label: 'Received', data: C.received, backgroundColor: '#e7c4c9', borderRadius: 4 }
    ]},
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, grid: { color: '#eef0f3' } }, x: { grid: { display: false } } } }
});
</script>
@endsection
