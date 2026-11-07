@extends('layouts.master')
@section('title') Collections @endsection

@section('content')
@include('includes.flash_message')

<div class="cc-page-head">
    <div><h4>Collections</h4><div class="sub">Categories and occasions such as Wedding, Birthday, Graduation. These become the website menu.</div></div>
    @can('create products')<button class="btn btn-primary btn-sm" onclick="openCol()"><i class="ri-add-line me-1"></i> New Collection</button>@endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table cc-table align-middle">
            <thead><tr><th style="width:36px"></th><th style="width:60px"></th><th>Name</th><th>Occasion</th><th>Yearly reminders</th><th>Cakes</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="sortRows">
            @forelse($collections as $c)
                <tr data-id="{{ $c->id }}">
                    <td class="cc-handle" title="Drag to reorder"><i class="ri-draggable"></i></td>
                    <td><img src="{{ $c->image_url }}" class="cc-thumb" alt=""></td>
                    <td><div class="cc-name">{{ $c->name }}</div><div class="text-muted small">/{{ $c->slug }}</div></td>
                    <td>{{ $c->occasion_type ? ($occasionTypes[$c->occasion_type] ?? $c->occasion_type) : '-' }}</td>
                    <td>{!! $c->is_recurring ? '<span class="cc-status ok">Remind yearly</span>' : '<span class="text-muted">No</span>' !!}</td>
                    <td>{{ $c->products_count }}</td>
                    <td><span class="cc-status {{ $c->is_active ? 'ok' : 'bad' }}">{{ $c->is_active ? 'Active' : 'Hidden' }}</span></td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-soft-primary" onclick="openCol({{ Js::from($c->only(['id','name','description','occasion_type','is_recurring','sort_order','is_active'])) }})">Edit</button>
                        <form method="POST" action="{{ route('collections.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete this collection? Cakes inside are kept.')">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No collections yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="colModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" enctype="multipart/form-data" id="colForm" action="{{ route('collections.store') }}">
        @csrf <span id="colMethod"></span>
        <div class="modal-header"><h5 class="modal-title" id="colTitle">New Collection</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" id="c_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="c_description" rows="2" class="form-control"></textarea></div>
            <div class="row g-3">
                <div class="col-7"><label class="form-label">Occasion type</label>
                    <select name="occasion_type" id="c_occasion_type" class="form-select"><option value="">None</option>@foreach($occasionTypes as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="col-5"><label class="form-label">Sort order</label><input type="number" name="sort_order" id="c_sort_order" value="0" class="form-control"></div>
            </div>
            <div class="mt-3"><label class="form-label">Cover image</label><input type="file" name="image" accept="image/*" class="form-control"></div>
            <div class="form-check form-switch mt-3"><input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="c_is_recurring"><label class="form-check-label" for="c_is_recurring">Remind customers yearly (e.g. birthdays)</label></div>
            <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="c_is_active" checked><label class="form-check-label" for="c_is_active">Show on website</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('script')
@include('includes.sortable', ['url' => route('collections.reorder')])
<script>
function openCol(c){
    const f=document.getElementById('colForm');
    f.reset();
    document.getElementById('colMethod').innerHTML='';
    document.getElementById('c_is_active').checked=true;
    if(c){
        f.action=`{{ url('collections') }}/${c.id}`;
        document.getElementById('colMethod').innerHTML='<input type="hidden" name="_method" value="PUT">';
        document.getElementById('colTitle').textContent='Edit Collection';
        ['name','description','occasion_type','sort_order'].forEach(k=>document.getElementById('c_'+k).value=c[k]??'');
        document.getElementById('c_is_recurring').checked=!!c.is_recurring;
        document.getElementById('c_is_active').checked=!!c.is_active;
    } else {
        f.action=`{{ route('collections.store') }}`;
        document.getElementById('colTitle').textContent='New Collection';
    }
    new bootstrap.Modal(document.getElementById('colModal')).show();
}
</script>
@endsection
