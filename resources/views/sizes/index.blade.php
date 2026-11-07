@extends('layouts.master')
@section('title') Sizes @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Sizes</h4><div class="sub">The size list used for every cake price grid (0.5 kg, 1 kg, 2 tier...).</div></div>
    @can('create products')<button class="btn btn-primary btn-sm" onclick="openSz()"><i class="ri-add-line me-1"></i> New Size</button>@endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th style="width:36px"></th><th>Label</th><th>Weight (kg)</th><th>Serves</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="sortRows">
            @forelse($sizes as $s)
                <tr data-id="{{ $s->id }}">
                    <td class="cc-handle" title="Drag to reorder"><i class="ri-draggable"></i></td>
                    <td class="cc-name">{{ $s->label }}</td>
                    <td>{{ $s->weight_kg !== null ? rtrim(rtrim($s->weight_kg, '0'), '.') : '-' }}</td>
                    <td>{{ $s->serves ?: '-' }}</td>
                    <td class="cc-order">{{ $s->sort_order }}</td>
                    <td><span class="cc-status {{ $s->is_active ? 'ok' : 'bad' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-soft-primary" onclick="openSz({{ Js::from($s->only(['id','label','weight_kg','serves','sort_order','is_active'])) }})">Edit</button>
                        <form method="POST" action="{{ route('sizes.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Delete this size? Its prices on cakes will be removed.')">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No sizes yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="szModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="szForm" action="{{ route('sizes.store') }}">
        @csrf <span id="szMethod"></span>
        <div class="modal-header"><h5 class="modal-title" id="szTitle">New Size</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Label *</label><input name="label" id="s_label" class="form-control" required placeholder="1 kg"></div>
            <div class="row g-3 mb-3">
                <div class="col-6"><label class="form-label">Weight (kg)</label><input type="number" step="0.01" name="weight_kg" id="s_weight_kg" class="form-control"></div>
                <div class="col-6"><label class="form-label">Serves</label><input name="serves" id="s_serves" class="form-control" placeholder="8-10"></div>
            </div>
            <div class="mb-3"><label class="form-label">Sort order</label><input type="number" name="sort_order" id="s_sort_order" value="0" class="form-control"></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="s_is_active" checked><label class="form-check-label" for="s_is_active">Active</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('script')
@include('includes.sortable', ['url' => route('sizes.reorder')])
<script>
function openSz(s){
    const form=document.getElementById('szForm'); form.reset();
    document.getElementById('szMethod').innerHTML='';
    document.getElementById('s_is_active').checked=true;
    if(s){
        form.action=`{{ url('sizes') }}/${s.id}`;
        document.getElementById('szMethod').innerHTML='<input type="hidden" name="_method" value="PUT">';
        document.getElementById('szTitle').textContent='Edit Size';
        ['label','weight_kg','serves','sort_order'].forEach(k=>document.getElementById('s_'+k).value=s[k]??'');
        document.getElementById('s_is_active').checked=!!s.is_active;
    } else { form.action=`{{ route('sizes.store') }}`; document.getElementById('szTitle').textContent='New Size'; }
    new bootstrap.Modal(document.getElementById('szModal')).show();
}
</script>
@endsection
