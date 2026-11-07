@extends('layouts.master')
@section('title') Expenses @endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Financials @endslot
    @slot('title') Expense Ledger @endslot
@endcomponent

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card shadow-none border">
            <div class="card-body">
                <form action="{{ route('expenses.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Supplier</label>
                            <select class="form-select select2-filter" name="supplier_id">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select select2-filter" name="expense_category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('expense_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-2-line"></i> Filter</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-soft-secondary">Clear</a>
                            
                            <button type="button" class="btn btn-success w-100" onclick="exportExpenses()">
                                <i class="ri-file-excel-2-line"></i> Export
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Expenses</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-soft-secondary">Manage Suppliers</a>
                    <a href="{{ route('expense-categories.index') }}" class="btn btn-soft-secondary">Manage Categories</a>
                    <button type="button" class="btn btn-primary" onclick="openExpenseModal()">
                        <i class="ri-add-line align-bottom me-1"></i> Record Expense
                    </button>
                    <button type="button" class="btn btn-info" onclick="openImportModal()">
                        <i class="ri-upload-cloud-2-line"></i> Import
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Ref #</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th class="text-end">Before Tax</th>
                            <th class="text-end">Tax (VAT)</th>
                            <th class="text-end">Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $exp)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M, Y') }}</td>
                            <td><span class="fw-medium">{{ $exp->reference_number ?? '-' }}</span></td>
                            <td><span class="badge bg-info-subtle text-info">{{ $exp->category->name ?? 'N/A' }}</span></td>
                            <td>{{ $exp->supplier->name ?? 'None' }}</td>
                            <td class="text-end">{{ number_format($exp->amount_before_tax, 2) }}</td>
                            <td class="text-end">{{ number_format($exp->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($exp->total_amount, 2) }}</td>
                            <td>
                                <button class="btn btn-sm btn-soft-primary" onclick="editExpense({{ $exp->id }})">Edit</button>
                                <button class="btn btn-sm btn-soft-danger" onclick="deleteExpense({{ $exp->id }})">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="expenseForm">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="expModalLabel">Record Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" id="exp_id" name="id">
                    
                    <div class="col-md-6">
                        <label class="form-label">Supplier (Vendor)</label>
                        <select class="form-select select2" id="exp_supplier_id" name="supplier_id" style="width:100%">
                            <option value="">No Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="exp_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="exp_category_id" name="expense_category_id" style="width:100%" required>
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Receipt / Ref Number</label>
                        <input type="text" class="form-control" id="exp_ref" name="reference_number">
                    </div>

                    <hr class="mt-4 mb-2">

                    <div class="col-md-4">
                        <label class="form-label text-muted">Amount Before Tax (KES) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control calc-expense" id="exp_subtotal" name="amount_before_tax" value="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Sales Tax (VAT) (KES)</label>
                        <input type="number" step="0.01" class="form-control calc-expense" id="exp_tax" name="tax_amount" value="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Total Amount (KES)</label>
                        <input type="text" class="form-control fw-bold bg-light text-danger" id="exp_total" readonly value="0.00">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="exp_method" name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Memo / Notes</label>
                        <textarea class="form-control" id="exp_notes" name="notes" rows="1"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveExpBtn">Save Expense</button>
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
                        Download the <a href="{{ route('expenses.template') }}" class="fw-bold">CSV Template here</a>, fill it out, and upload it below.
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const expModal = new bootstrap.Modal(document.getElementById('expenseModal'));

    $(document).ready(function() {
        $('.select2').select2({ dropdownParent: $('#expenseModal') });
        $('.select2-filter').select2(); // Init the filter dropdowns outside the modal
    });

    // Submits the current filter parameters to the export route!
    function exportExpenses() {
        let form = document.getElementById('filterForm');
        form.action = "{{ route('expenses.export') }}";
        form.submit();
        form.action = "{{ route('expenses.index') }}"; // Reset back to normal filtering
    }

    // Real-Time Math
    document.querySelectorAll('.calc-expense').forEach(input => {
        input.addEventListener('input', function() {
            let subtotal = parseFloat(document.getElementById('exp_subtotal').value) || 0;
            let tax = parseFloat(document.getElementById('exp_tax').value) || 0;
            document.getElementById('exp_total').value = (subtotal + tax).toFixed(2);
        });
    });

    function openExpenseModal() {
        document.getElementById('expenseForm').reset();
        document.getElementById('exp_id').value = '';
        $('#exp_supplier_id').val('').trigger('change');
        $('#exp_category_id').val('').trigger('change');
        document.getElementById('expModalLabel').innerText = 'Record Expense';
        document.getElementById('exp_total').value = '0.00';
        expModal.show();
    }

    function editExpense(id) {
        axios.get(`/expenses/${id}/edit`).then(res => {
            let exp = res.data;
            document.getElementById('exp_id').value = exp.id;
            $('#exp_supplier_id').val(exp.supplier_id).trigger('change');
            $('#exp_category_id').val(exp.expense_category_id).trigger('change');
            document.getElementById('exp_date').value = exp.expense_date;
            document.getElementById('exp_ref').value = exp.reference_number || '';
            document.getElementById('exp_subtotal').value = exp.amount_before_tax;
            document.getElementById('exp_tax').value = exp.tax_amount;
            document.getElementById('exp_total').value = exp.total_amount;
            document.getElementById('exp_method').value = exp.payment_method;
            document.getElementById('exp_notes').value = exp.notes || '';
            
            document.getElementById('expModalLabel').innerText = 'Edit Expense';
            expModal.show();
        });
    }

    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('exp_id').value;
        let url = id ? `/expenses/${id}` : '/expenses';
        let method = id ? 'PUT' : 'POST';
        
        let formData = new FormData(this);
        let dataObj = Object.fromEntries(formData);
        
        document.getElementById('saveExpBtn').disabled = true;

        axios({ method: method, url: url, data: dataObj })
            .then(res => { expModal.hide(); location.reload(); })
            .catch(err => {
                Swal.fire('Error', 'Please verify all required fields.', 'error');
                document.getElementById('saveExpBtn').disabled = false;
            });
    });

    function deleteExpense(id) {
        Swal.fire({ title: 'Delete Expense?', text: "This will remove the transaction from your ledger.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!' })
        .then((result) => {
            if (result.isConfirmed) axios.delete(`/expenses/${id}`).then(res => location.reload());
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

        axios.post('{{ route('expenses.import') }}', formData)
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