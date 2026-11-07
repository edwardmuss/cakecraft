@extends('layouts.master')
@section('title') Occasion Reminders @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div>
        <h4>Occasion Reminders</h4>
        <div class="sub">Customers who ordered a birthday or anniversary cake before, with the date coming up again.</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addOcc"><i class="ri-add-line me-1"></i> Add occasion</button>
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#settingsModal"><i class="ri-settings-3-line me-1"></i> Message &amp; settings</button>
    </div>
</div>

<div class="cc-kpis">
    <div class="cc-kpi accent"><div class="lbl">Coming up ({{ $leadDays }} days)</div><div class="val">{{ $stats['upcoming'] }}</div></div>
    <div class="cc-kpi"><div class="lbl">Waiting for a reminder</div><div class="val">{{ $stats['pending'] }}</div></div>
    <div class="cc-kpi"><div class="lbl">Already reminded</div><div class="val">{{ $stats['sent'] }}</div></div>
    <div class="cc-kpi"><div class="lbl">Occasions tracked</div><div class="val">{{ $stats['tracked'] }}</div></div>
</div>

<form method="POST" action="{{ route('occasions.send-bulk') }}" id="bulkForm">@csrf
<div class="card">
    <div class="cc-toolbar">
        <span class="small text-muted">Show next</span>
        @foreach([7,14,30,60,90] as $d)
            <a href="{{ route('occasions.index', ['days'=>$d, 'type'=>$request->type]) }}" class="btn btn-sm {{ $leadDays==$d ? 'btn-primary' : 'btn-light' }}">{{ $d }} days</a>
        @endforeach
        <select class="form-select ms-2" onchange="location='{{ route('occasions.index', ['days'=>$leadDays]) }}&type='+this.value">
            <option value="">All types</option>
            @foreach($types as $k=>$l)<option value="{{ $k }}" @selected($request->type==$k)>{{ $l }}</option>@endforeach
        </select>
        <div class="ms-auto">
            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Send the reminder SMS to all selected customers?')"><i class="ri-send-plane-line me-1"></i> Send to selected</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th style="width:36px"><input type="checkbox" class="form-check-input" onclick="document.querySelectorAll('.rowchk').forEach(c=>{if(!c.disabled)c.checked=this.checked})"></th><th>When</th><th>Customer</th><th>Occasion</th><th>Last cake ordered</th><th>Message</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($upcoming as $o)
                @php $cls = $o->days_away == 0 ? 'today' : ($o->days_away <= 7 ? 'soon' : ''); @endphp
                <tr>
                    <td><input type="checkbox" class="form-check-input rowchk" name="ids[]" value="{{ $o->id }}" @disabled($o->reminded)></td>
                    <td><div class="cc-day-pill {{ $cls }}">{{ $o->days_away == 0 ? 'Today' : $o->days_away }}<small>{{ $o->days_away == 0 ? $o->next_date->format('j M') : 'days · '.$o->next_date->format('j M') }}</small></div></td>
                    <td><div class="cc-name">{{ $o->customer->name }}</div><div class="text-muted small">{{ $o->customer->phone }}</div></td>
                    <td>{{ $types[$o->occasion_type] ?? ucfirst($o->occasion_type) }}@if($o->recipient_name)<div class="text-muted small">for {{ $o->recipient_name }}</div>@endif</td>
                    <td>{{ $o->last_cake ?: '-' }}<div class="text-muted small">{{ $o->last_occasion_date?->format('j M Y') }}</div></td>
                    <td style="max-width:300px"><div class="cc-msg">{{ \Illuminate\Support\Str::limit($o->preview, 130) }}</div></td>
                    <td>@if($o->reminded)<span class="cc-status ok">Reminded</span>@else<span class="cc-status warn">Not yet</span>@endif</td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-soft-primary" onclick="openSend({{ $o->id }}, {{ Js::from($o->customer->name) }}, {{ Js::from($o->preview) }})">{{ $o->reminded ? 'Resend' : 'Send' }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">Nothing coming up in the next {{ $leadDays }} days.<br><small>Occasions are saved automatically when you invoice a cake with an occasion date, or add them manually.</small></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</form>

<div class="card">
    <div class="card-header"><h6 class="mb-0">Recent reminders</h6></div>
    <div class="table-responsive">
        <table class="table cc-table">
            <thead><tr><th>Sent</th><th>Customer</th><th>For</th><th>Message</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($history as $h)
                <tr>
                    <td>{{ $h->sent_at?->format('j M, H:i') }}</td>
                    <td>{{ $h->occasion->customer->name ?? '-' }}</td>
                    <td>{{ $types[$h->occasion->occasion_type] ?? '' }} {{ $h->year }}</td>
                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit($h->message, 110) }}</td>
                    <td><span class="cc-status {{ $h->status=='sent' ? 'ok' : 'bad' }}">{{ ucfirst($h->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No reminders sent yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Send single --}}
<div class="modal fade" id="sendModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="sendForm">@csrf
        <div class="modal-header"><h5 class="modal-title">Send reminder to <span id="sendName"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label class="form-label">Message (edit if you like)</label><textarea name="message" id="sendMsg" rows="5" class="form-control" maxlength="600"></textarea></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Send SMS</button></div>
    </form>
</div></div></div>

{{-- Settings --}}
<div class="modal fade" id="settingsModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" action="{{ route('occasions.settings') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Reminder message &amp; settings</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <label class="form-label">Message template</label>
            <textarea name="template" rows="5" class="form-control" required maxlength="600">{{ $template }}</textarea>
            <div class="form-text mb-3">Placeholders: {customer} {recipient} {occasion} {last_cake} {date} {last_year} {business}</div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Remind how many days before?</label><input type="number" name="lead_days" min="1" max="120" value="{{ $leadDays }}" class="form-control"></div>
                <div class="col-md-8 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="auto_send" value="1" id="autoSend" @checked($autoSend)><label class="form-check-label" for="autoSend">Send automatically every morning (needs the scheduler running)</label></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>

{{-- Add occasion --}}
<div class="modal fade" id="addOcc" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('occasions.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Add an occasion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <p class="text-muted small">For cakes you made before this system, or customers who told you a date.</p>
            <div class="mb-3"><label class="form-label">Customer *</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">Select...</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }} · {{ $c->phone }}</option>@endforeach
                </select></div>
            <div class="row g-3 mb-3">
                <div class="col-6"><label class="form-label">Occasion *</label><select name="occasion_type" class="form-select" required>@foreach($types as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="col-6"><label class="form-label">Date of last cake *</label><input type="date" name="date" class="form-control" required></div>
            </div>
            <div class="mb-3"><label class="form-label">Celebrant</label><input name="recipient_name" class="form-control" placeholder="e.g. Brian"></div>
            <div><label class="form-label">Cake ordered</label><input name="last_cake" class="form-control" placeholder="Black Forest, 2 kg"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('script')
<script>
function openSend(id, name, msg){
    document.getElementById('sendForm').action = `{{ url('occasions') }}/${id}/send`;
    document.getElementById('sendName').textContent = name;
    document.getElementById('sendMsg').value = msg;
    new bootstrap.Modal(document.getElementById('sendModal')).show();
}
</script>
@endsection
