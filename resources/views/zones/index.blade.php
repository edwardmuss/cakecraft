@extends('layouts.master')
@section('title') Delivery Zones @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Delivery Zones</h4><div class="sub">Areas you deliver to and the fee for each. Customers pick a zone at checkout. If you add none, delivery is quoted when you confirm the order.</div></div>
    @can('create products')<button class="btn btn-primary btn-sm" onclick="openZn()"><i class="ri-add-line me-1"></i> New Zone</button>@endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th style="width:36px"></th><th>Zone</th><th class="text-end">Fee (KES)</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="sortRows">
            @forelse($zones as $z)
                <tr data-id="{{ $z->id }}">
                    <td class="cc-handle" title="Drag to reorder"><i class="ri-draggable"></i></td>
                    <td class="cc-name">{{ $z->name }}</td>
                    <td class="text-end">{{ number_format($z->fee, 2) }}</td>
                    <td class="cc-order">{{ $z->sort_order }}</td>
                    <td><span class="cc-status {{ $z->is_active ? 'ok' : 'bad' }}">{{ $z->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-soft-primary" onclick="openZn({{ Js::from($z->only(['id','name','fee','sort_order','is_active'])) }})">Edit</button>
                        <form method="POST" action="{{ route('zones.destroy', $z->id) }}" class="d-inline" onsubmit="return confirm('Delete this zone?')">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No delivery zones yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="znModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="znForm" action="{{ route('zones.store') }}">
        @csrf <span id="znMethod"></span>
        <div class="modal-header"><h5 class="modal-title" id="znTitle">New Zone</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Zone name *</label><input name="name" id="z_name" class="form-control" required placeholder="e.g. Nairobi CBD"></div>
            <div class="mb-3"><label class="form-label">Delivery fee (KES) *</label><input type="number" step="0.01" min="0" name="fee" id="z_fee" class="form-control" required value="0"></div>
            <div class="mb-3"><label class="form-label">Sort order</label><input type="number" name="sort_order" id="z_sort_order" value="0" class="form-control"></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="z_is_active" checked><label class="form-check-label" for="z_is_active">Active</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('script')
@include('includes.sortable', ['url' => route('zones.reorder')])
<script>
function openZn(z){
    const form=document.getElementById('znForm'); form.reset();
    document.getElementById('znMethod').innerHTML='';
    document.getElementById('z_is_active').checked=true;
    if(z){
        form.action=`{{ url('delivery-zones') }}/${z.id}`;
        document.getElementById('znMethod').innerHTML='<input type="hidden" name="_method" value="PUT">';
        document.getElementById('znTitle').textContent='Edit Zone';
        ['name','fee','sort_order'].forEach(k=>document.getElementById('z_'+k).value=z[k]??'');
        document.getElementById('z_is_active').checked=!!z.is_active;
    } else { form.action=`{{ route('zones.store') }}`; document.getElementById('znTitle').textContent='New Zone'; }
    new bootstrap.Modal(document.getElementById('znModal')).show();
}
</script>
@endsection
