@extends('layouts.master')
@section('title') Expense Categories @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Expenses @endslot
    @slot('title') Expense Categories @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Category List</h5>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        <i class="ri-add-line align-bottom me-1"></i> Add Category
                    </button>

                    <a href="{{ route('expense-categories.export') }}" class="btn btn-success">
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
                            <th>Category Name</th>
                            <th width="150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr id="row_{{ $cat->id }}">
                            <td class="fw-medium">{{ $cat->name }}</td>
                            <td>
                                <button class="btn btn-sm btn-soft-primary" onclick="editCategory({{ $cat->id }})">Edit</button>
                                <button class="btn btn-sm btn-soft-danger" onclick="deleteCategory({{ $cat->id }})">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="catForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cat_id">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
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
                        Download the <a href="{{ route('expense-categories.template') }}" class="fw-bold">CSV Template here</a>, fill it out, and upload it below.
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
    const myModal = new bootstrap.Modal(document.getElementById('catModal'));

    function openModal() {
        document.getElementById('catForm').reset();
        document.getElementById('cat_id').value = '';
        document.getElementById('modalLabel').innerText = 'Add Category';
        $('.error-text').text('');
        myModal.show();
    }

    function editCategory(id) {
        axios.get(`/expense-categories/${id}/edit`).then(res => {
            document.getElementById('cat_id').value = res.data.id;
            document.getElementById('name').value = res.data.name;
            document.getElementById('modalLabel').innerText = 'Edit Category';
            myModal.show();
        });
    }

    document.getElementById('catForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('cat_id').value;
        let url = id ? `/expense-categories/${id}` : '/expense-categories';
        let method = id ? 'PUT' : 'POST';
        
        axios({ method: method, url: url, data: { name: document.getElementById('name').value } })
            .then(res => { myModal.hide(); location.reload(); })
            .catch(err => { 
                if(err.response.status === 422) $('.name_error').text(err.response.data.errors.name[0]); 
            });
    });

    function deleteCategory(id) {
        Swal.fire({ title: 'Delete Category?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete!' })
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/expense-categories/${id}`).then(res => location.reload()).catch(err => Swal.fire('Error', err.response.data.message, 'error'));
            }
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

        axios.post('{{ route('expense-categories.import') }}', formData)
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