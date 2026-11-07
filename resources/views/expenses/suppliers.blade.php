@extends('layouts.master')
@section('title') Suppliers @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Expenses @endslot
    @slot('title') Suppliers @endslot
@endcomponent

<div class="row mb-3">
    <div class="col-lg-12">
        <form action="{{ route('suppliers.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search suppliers..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Supplier List</h5>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        <i class="ri-add-line align-bottom me-1"></i> Add Supplier
                    </button>

                    <a href="{{ route('suppliers.export') }}" class="btn btn-success">
                        <i class="ri-file-excel-2-line me-1"></i> Export
                    </a>

                    <button type="button" class="btn btn-info" onclick="openImportModal()">
                        <i class="ri-upload-cloud-2-line me-1"></i> Import
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Supplier Name</th>
                            <th>Contact Info</th>
                            <th>KRA PIN</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $sup)
                        <tr>
                            <td class="fw-medium">{{ $sup->name }}</td>
                            <td>
                                {{ $sup->phone }}<br>
                                <small class="text-muted">{{ $sup->email }}</small>
                            </td>
                            <td>{{ $sup->kra_pin ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-soft-primary" onclick="editSupplier({{ $sup->id }})">Edit</button>
                                <button class="btn btn-sm btn-soft-danger" onclick="deleteSupplier({{ $sup->id }})">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="supModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="supForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="supModalLabel">Add Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" id="sup_id">
                    <div class="col-12">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="s_name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="s_phone" name="phone">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="s_email" name="email">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">KRA PIN</label>
                        <input type="text" class="form-control" id="s_kra" name="kra_pin">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="s_address" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Download the <a href="{{ route('suppliers.template') }}" class="fw-bold">CSV Template here</a>, fill it out, and upload it below.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select File (.csv, .xlsx)</label>
                        <input type="file" class="form-control" id="importFile" name="file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="uploadBtn">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const supModal = new bootstrap.Modal(document.getElementById('supModal'));

    function openModal() {
        document.getElementById('supForm').reset();
        document.getElementById('sup_id').value = '';
        document.getElementById('supModalLabel').innerText = 'Add Supplier';
        supModal.show();
    }

    function editSupplier(id) {
        axios.get(`/suppliers/${id}/edit`).then(res => {
            document.getElementById('sup_id').value = res.data.id;
            document.getElementById('s_name').value = res.data.name;
            document.getElementById('s_phone').value = res.data.phone || '';
            document.getElementById('s_email').value = res.data.email || '';
            document.getElementById('s_kra').value = res.data.kra_pin || '';
            document.getElementById('s_address').value = res.data.address || '';
            document.getElementById('supModalLabel').innerText = 'Edit Supplier';
            supModal.show();
        });
    }

    document.getElementById('supForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('sup_id').value;
        let url = id ? `/suppliers/${id}` : '/suppliers';
        let method = id ? 'PUT' : 'POST';
        let formData = new FormData(this);
        
        axios({ method: method, url: url, data: Object.fromEntries(formData) })
            .then(res => { supModal.hide(); location.reload(); })
            .catch(err => Swal.fire('Error', 'Check validation.', 'error'));
    });

    function deleteSupplier(id) {
        Swal.fire({ title: 'Delete Supplier?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete!' })
        .then((result) => {
            if (result.isConfirmed) axios.delete(`/suppliers/${id}`).then(res => location.reload());
        });
    }

    const importModal = new bootstrap.Modal(document.getElementById('importModal'));

    function openImportModal() {
        document.getElementById('importForm').reset();
        importModal.show();
    }

    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData();
        formData.append('file', document.getElementById('importFile').files[0]);

        let uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Importing...';

        axios.post('{{ route('suppliers.import') }}', formData)
        .then(response => {
            importModal.hide();
            Swal.fire('Success', response.data.success, 'success').then(() => location.reload());
        })
        .catch(error => {
            Swal.fire('Error', 'Check your file format or column names.', 'error');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = 'Upload & Import';
        });
    });
</script>
@endsection