@extends('layouts.master')
@section('title') Customers @endsection 
@section('css')
    {{-- Css for customers --}}
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Customers @endslot
@endcomponent

<div class="row gy-2 mb-2">
    @include('includes.flash_message')
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <form action="{{ route('customers.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-10">
                                    <div class="search-box">
                                        <input type="text" name="search" class="form-control search" placeholder="Search by name, organization, phone, or email..." value="{{ request('search') }}">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                    @if(request()->has('search'))
                                        <a href="{{ route('customers.index') }}" class="btn btn-soft-secondary">Clear</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Customer List</h5>

                <div class="d-flex gap-2">
                    <a href="{{ route('customers.export') }}" class="btn btn-success">
                        <i class="ri-file-excel-2-line"></i> Export Excel
                    </a>

                    <button type="button" class="btn btn-info" onclick="openImportModal()">
                        <i class="ri-upload-cloud-2-line"></i> Import
                    </button>

                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        <i class="ri-add-line align-bottom me-1"></i> Add Customer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name & Organization</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Addresses</th>
                                <th>Wallet Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody">
                            @foreach($customers as $customer)
                            <tr id="row_{{ $customer->id }}">
                                <td>
                                    <strong>{{ $customer->name }}</strong><br>
                                    <span class="text-muted fs-12"><i class="ri-building-line align-bottom"></i> {{ $customer->organization_name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>
                                    <div class="fs-12">
                                        <strong>Billing:</strong> {{ Str::limit($customer->billing_address, 30) ?? 'N/A' }}<br>
                                        <strong>Delivery:</strong> <span class="text-info">{{ Str::limit($customer->delivery_location, 30) ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>KES {{ number_format($customer->wallet_balance, 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-soft-info" onclick="editCustomer({{ $customer->id }})">Edit</button>
                                    <button class="btn btn-sm btn-soft-danger" onclick="deleteCustomer({{ $customer->id }})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($customers, 'links'))
                <div class="mt-3">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="customerForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="customerId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <span class="text-danger error-text name_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="organization_name" class="form-label">Company (optional)</label>
                            <input type="text" class="form-control" id="organization_name" name="organization_name">
                            <span class="text-danger error-text organization_name_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number (WhatsApp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            <span class="text-danger error-text phone_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <span class="text-danger error-text email_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="billing_address" class="form-label">Billing Address</label>
                            <textarea class="form-control" id="billing_address" name="billing_address" rows="3"></textarea>
                            <span class="text-danger error-text billing_address_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="delivery_location" class="form-label">Delivery Location</label>
                            <textarea class="form-control" id="delivery_location" name="delivery_location" rows="3"></textarea>
                            <span class="text-danger error-text delivery_location_error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Customers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <p class="mb-2">Upload your customer list via Excel or CSV. The system will automatically skip duplicate phone numbers and format them.</p>
                        <a href="{{ route('customers.template') }}" class="btn btn-sm btn-primary">
                            <i class="ri-download-line align-bottom me-1"></i> Download Template
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info" id="uploadBtn">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

<script>
    // Setup Axios to automatically include CSRF token
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));

    function openModal() {
        document.getElementById('customerForm').reset();
        document.getElementById('customerId').value = '';
        document.getElementById('customerModalLabel').innerText = 'Add Customer';
        $('.error-text').text(''); // Clear previous errors
        customerModal.show();
    }

    function editCustomer(id) {
        $('.error-text').text('');
        axios.get(`/customers/${id}/edit`)
            .then(response => {
                const customer = response.data;
                document.getElementById('customerId').value = customer.id;
                document.getElementById('name').value = customer.name;
                document.getElementById('organization_name').value = customer.organization_name || '';
                document.getElementById('phone').value = customer.phone;
                document.getElementById('email').value = customer.email || '';
                document.getElementById('billing_address').value = customer.billing_address || '';
                document.getElementById('delivery_location').value = customer.delivery_location || '';
                
                document.getElementById('customerModalLabel').innerText = 'Edit Customer';
                customerModal.show();
            })
            .catch(error => {
                Swal.fire('Error', 'Could not fetch customer data', 'error');
            });
    }

    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('customerId').value;
        let url = id ? `/customers/${id}` : '/customers';
        let method = id ? 'PUT' : 'POST';
        
        let formData = {
            name: document.getElementById('name').value,
            organization_name: document.getElementById('organization_name').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            billing_address: document.getElementById('billing_address').value,
            delivery_location: document.getElementById('delivery_location').value,
        };

        // Disable button to prevent double submit
        document.getElementById('saveBtn').disabled = true;
        $('.error-text').text(''); 

        axios({
            method: method,
            url: url,
            data: formData
        })
        .then(response => {
            customerModal.hide();
            Swal.fire('Success', response.data.success, 'success').then(() => {
                location.reload(); 
            });
        })
        .catch(error => {
            if(error.response && error.response.status === 422) {
                let errors = error.response.data.errors;
                for (const key in errors) {
                    $('.' + key + '_error').text(errors[key][0]);
                }
            } else {
                Swal.fire('Error', 'Something went wrong!', 'error');
            }
        })
        .finally(() => {
            document.getElementById('saveBtn').disabled = false;
        });
    });

    function deleteCustomer(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/customers/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success');
                        document.getElementById('row_' + id).remove();
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Could not delete customer.', 'error');
                    });
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
        let fileInput = document.getElementById('importFile');
        
        if(fileInput.files.length === 0) return;

        formData.append('file', fileInput.files[0]);

        // Show loading state (imports can take a few seconds)
        let uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importing...';

        axios.post('{{ route('customers.import') }}', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(response => {
            importModal.hide();
            Swal.fire({
                title: 'Import Successful',
                text: response.data.success,
                icon: 'success'
            }).then(() => {
                location.reload(); 
            });
        })
        .catch(error => {
            let msg = 'Something went wrong during import.';
            if(error.response && error.response.data && error.response.data.errors) {
                msg = error.response.data.errors.file[0];
            }
            Swal.fire('Error', msg, 'error');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = 'Upload & Import';
        });
    });
</script>
@endsection