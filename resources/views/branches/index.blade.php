@extends('layouts.master')
@section('title') Branches @endsection 
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Settings @endslot
    @slot('title') Branches @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Branch List</h5>
                <button type="button" class="btn btn-primary" onclick="openBranchModal()">
                    <i class="ri-add-line align-bottom me-1"></i> Add Branch
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email / Phone</th>
                                <th>VAT %</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $branch)
                            <tr id="row_{{ $branch->id }}">
                                <td><strong>{{ $branch->branch_name }}</strong><br><small class="text-muted">KRA: {{ $branch->kra_pin_number ?? 'N/A' }}</small></td>
                                <td>{{ $branch->branch_email }}<br>{{ $branch->company_phone }}</td>
                                <td>{{ $branch->vat_percentage }}%</td>
                                <td>
                                    @if($branch->isActive)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                    
                                    @if($branch->locked)
                                        <i class="ri-lock-2-fill text-warning ms-1" title="Locked"></i>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-soft-info" onclick="editBranch({{ $branch->id }})">Edit</button>
                                    <button class="btn btn-sm btn-soft-danger" onclick="deleteBranch({{ $branch->id }})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="branchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="branchForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="branchModalLabel">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="branch_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branch_name" name="branch_name" required>
                            <span class="text-danger error-text branch_name_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">KRA PIN Number</label>
                            <input type="text" class="form-control" id="kra_pin_number" name="kra_pin_number">
                            <span class="text-danger error-text kra_pin_number_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="branch_email" name="branch_email" required>
                            <span class="text-danger error-text branch_email_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" required>
                            <span class="text-danger error-text company_phone_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">VAT Percentage</label>
                            <input type="number" step="0.01" class="form-control" id="vat_percentage" name="vat_percentage" value="16.00">
                            <span class="text-danger error-text vat_percentage_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" id="website" name="website">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">About</label>
                            <textarea class="form-control" id="about" name="about" rows="2"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Payment Details</label>
                            <textarea class="form-control" id="payment_details" name="payment_details" rows="2" placeholder="e.g., Please pay KES {Balance} via M-Pesa to {BranchPhone} using Account: {InvoiceNumber}"></textarea>
                            
                            <div class="mt-2 text-muted fs-12">
                                <p class="mb-1"><i class="ri-information-line align-middle"></i> Use the following tags to dynamically populate the PDF:</p>
                                <ul class="mb-0 ps-3">
                                    <li><code>{BranchName}</code> : The name of your branch</li>
                                    <li><code>{BranchPhone}</code> : Your company phone number</li>
                                    <li><code>{InvoiceNumber}</code> : The specific invoice number</li>
                                    <li><code>{TotalAmount}</code> : The grand total of the invoice</li>
                                    <li><code>{Balance}</code> : The remaining unpaid balance</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="isActive" checked>
                                <label class="form-check-label" for="isActive">Branch is Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBranchBtn">Save Branch</button>
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
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const branchModal = new bootstrap.Modal(document.getElementById('branchModal'));

    function openBranchModal() {
        document.getElementById('branchForm').reset();
        document.getElementById('branch_id').value = '';
        document.getElementById('branchModalLabel').innerText = 'Add Branch';
        document.getElementById('isActive').checked = true;
        $('.error-text').text('');
        branchModal.show();
    }

    function editBranch(id) {
        $('.error-text').text('');
        axios.get(`/branches/${id}/edit`)
            .then(response => {
                const b = response.data;
                document.getElementById('branch_id').value = b.id;
                document.getElementById('branch_name').value = b.branch_name;
                document.getElementById('branch_email').value = b.branch_email;
                document.getElementById('company_phone').value = b.company_phone;
                document.getElementById('kra_pin_number').value = b.kra_pin_number || '';
                document.getElementById('vat_percentage').value = b.vat_percentage;
                document.getElementById('payment_details').value = b.payment_details || '';
                document.getElementById('website').value = b.website || '';
                document.getElementById('about').value = b.about || '';
                document.getElementById('isActive').checked = b.isActive ? true : false;
                
                document.getElementById('branchModalLabel').innerText = 'Edit Branch';
                branchModal.show();
            });
    }

    $('#branchForm').submit(function(e) {
        e.preventDefault();
        
        let id = $('#branch_id').val();
        let url = id ? `/branches/${id}` : '/branches';
        let method = id ? 'PUT' : 'POST';
        
        // Simple Object mapping for Axios
        let formData = {
            branch_name: $('#branch_name').val(),
            branch_email: $('#branch_email').val(),
            company_phone: $('#company_phone').val(),
            kra_pin_number: $('#kra_pin_number').val(),
            vat_percentage: $('#vat_percentage').val(),
            payment_details: $('#payment_details').val(),
            website: $('#website').val(),
            about: $('#about').val(),
            isActive: $('#isActive').is(':checked') ? 1 : 0
        };

        $('#saveBranchBtn').prop('disabled', true);
        $('.error-text').text(''); 

        axios({ method: method, url: url, data: formData })
            .then(response => {
                branchModal.hide();
                Swal.fire('Success', response.data.success, 'success').then(() => location.reload());
            })
            .catch(error => {
                if(error.response && error.response.status === 422) {
                    let errors = error.response.data.errors;
                    if(errors.general) Swal.fire('Error', errors.general[0], 'error');
                    for (const key in errors) { $('.' + key + '_error').text(errors[key][0]); }
                }
            })
            .finally(() => $('#saveBranchBtn').prop('disabled', false));
    });

    function deleteBranch(id) {
        Swal.fire({
            title: 'Delete Branch?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/branches/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success');
                        document.getElementById('row_' + id).remove();
                    })
                    .catch(error => {
                        let msg = error.response.data.errors?.general[0] || 'Could not delete branch.';
                        Swal.fire('Error', msg, 'error');
                    });
            }
        });
    }
</script>
@endsection