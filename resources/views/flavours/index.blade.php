@extends('layouts.master')
@section('title') Flavours @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Flavours</h4><div class="sub">Sponge and filling options customers can choose.</div></div>
    @can('create products')<button class="btn btn-primary btn-sm" onclick="openFl()"><i class="ri-add-line me-1"></i> New Flavour</button>@endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th style="width:36px"></th><th>Flavour</th><th>Description</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="sortRows">
            @forelse($flavours as $f)
                <tr data-id="{{ $f->id }}">
                    <td class="cc-handle" title="Drag to reorder"><i class="ri-draggable"></i></td>
                    <td class="cc-name">{{ $f->name }}</td>
                    <td class="text-muted">{{ $f->description ?: '-' }}</td>
                    <td class="cc-order">{{ $f->sort_order }}</td>
                    <td><span class="cc-status {{ $f->is_active ? 'ok' : 'bad' }}">{{ $f->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-soft-primary" onclick="openFl({{ Js::from($f->only(['id','name','description','sort_order','is_active'])) }})">Edit</button>
                        <form method="POST" action="{{ route('flavours.destroy', $f->id) }}" class="d-inline" onsubmit="return confirm('Delete this flavour?')">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No flavours yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="flModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="flForm" action="{{ route('flavours.store') }}">
        @csrf <span id="flMethod"></span>
        <div class="modal-header"><h5 class="modal-title" id="flTitle">New Flavour</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" id="f_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><input name="description" id="f_description" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Sort order</label><input type="number" name="sort_order" id="f_sort_order" value="0" class="form-control"></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="f_is_active" checked><label class="form-check-label" for="f_is_active">Active</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('script')
@include('includes.sortable', ['url' => route('flavours.reorder')])
<script>
function openFl(f){
    const form=document.getElementById('flForm'); form.reset();
    document.getElementById('flMethod').innerHTML='';
    document.getElementById('f_is_active').checked=true;
    if(f){
        form.action=`{{ url('flavours') }}/${f.id}`;
        document.getElementById('flMethod').innerHTML='<input type="hidden" name="_method" value="PUT">';
        document.getElementById('flTitle').textContent='Edit Flavour';
        ['name','description','sort_order'].forEach(k=>document.getElementById('f_'+k).value=f[k]??'');
        document.getElementById('f_is_active').checked=!!f.is_active;
    } else { form.action=`{{ route('flavours.store') }}`; document.getElementById('flTitle').textContent='New Flavour'; }
    new bootstrap.Modal(document.getElementById('flModal')).show();
}
</script>
@endsection
