@extends('layouts.master')
@section('title') Invoices @endsection 
@section('css')
{{-- Css for invoices --}}
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Billing @endslot
    @slot('title') Invoices @endslot
@endcomponent

<div class="row gy-2 mb-2">
    @include('includes.flash_message')
</div>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('invoices.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Invoice # or Customer..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label d-block">Status</label>
                            <select name="status" id="status" class="form-select w-100">
                                <option value="">All Statuses</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Packed</option>
                                <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="occasion_date" class="form-label">Cake needed on</label>
                            <input type="date" name="occasion_date" id="occasion_date" class="form-control" value="{{ request('occasion_date') }}">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="ri-search-line me-1"></i> Search</button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-soft-secondary w-100">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Invoice List</h5>
                <div class="d-flex gap-2">
                    @can('view financials')
                    <button type="button" class="btn btn-success" onclick="exportExcel()">
                        <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Excel
                    </button>
                    @endcan
                    
                    @can('create invoices')
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                        <i class="ri-add-line align-bottom me-1"></i> Create Invoice
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Cake needed</th>
                                
                                {{-- RBAC: Hide Financials from Logistics/Packers --}}
                                @can('view financials')
                                    <th>Total (KES)</th>
                                    <th>Balance (KES)</th>
                                @endcan

                                {{-- RBAC: Show Delivery Location to Drivers --}}
                                @can('manage deliveries')
                                    <th>Delivery Location</th>
                                @endcan

                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                                        <tr id="row_{{ $invoice->id }}">
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>
                                    {{ $invoice->customer->organization_name ?? $invoice->customer->name ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ $invoice->customer->phone ?? '' }}</small>
                                </td>
                                <td>
                                    @if($invoice->occasion_date)
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($invoice->occasion_date)->format('D, d M Y') }}</span><br>
                                        <small class="text-muted">{{ $invoice->occasion_type ? ucfirst(str_replace('_', ' ', $invoice->occasion_type)) : '' }}{{ $invoice->recipient_name ? ' · ' . $invoice->recipient_name : '' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- RBAC: Financial Columns --}}
                                @can('view financials')
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="text-danger fw-bold">
                                    {{ number_format($invoice->total_amount - $invoice->amount_paid, 2) }}
                                </td>
                                @endcan

                                {{-- RBAC: Logistics Column --}}
                                @can('manage deliveries')
                                <td>
                                    <small class="text-muted">{{ Str::limit($invoice->customer->delivery_location, 30) ?? 'N/A' }}</small>
                                </td>
                                @endcan

                                <td>
                                    @if($invoice->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($invoice->status == 'processing')
                                        <span class="badge bg-primary">Processing</span>
                                    @elseif($invoice->status == 'partially_paid')
                                        <span class="badge bg-warning">Partially Paid</span>
                                    @elseif($invoice->status == 'packed')
                                        <span class="badge bg-secondary">Packed</span>
                                    @elseif($invoice->status == 'dispatched')
                                        <span class="badge bg-dark">Dispatched</span>
                                    @elseif($invoice->status == 'delivered')
                                        <span class="badge bg-info">Delivered</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            {{-- Everyone who can see the list can view details --}}
                                            <li>
                                                <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View Details
                                                </a>
                                            </li>

                                            @can('update invoices')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}">
                                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit Invoice
                                                </a>
                                            </li>
                                            @endcan

                                            {{-- Send Reminder (Only if unpaid balance exists and not cancelled) --}}
                                            @can('view financials')
                                                @if($invoice->total_amount > $invoice->amount_paid && $invoice->status !== 'cancelled')
                                                <li>
                                                    <button class="dropdown-item text-warning" onclick="sendReminder({{ $invoice->id }})">
                                                        <i class="ri-notification-3-line align-bottom me-2 text-warning"></i> 
                                                        Send Reminder {{ $invoice->reminder_count > 0 ? '(' . $invoice->reminder_count . ')' : '' }}
                                                    </button>
                                                </li>
                                                @endif
                                            @endcan

                                            @can('view financials')
                                            <li>
                                                <a class="dropdown-item text-primary" href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank">
                                                    <i class="ri-download-2-line align-bottom me-2 text-primary"></i> Download PDF
                                                </a>
                                            </li>
                                            @endcan

                                            @can('record payments')
                                                {{-- Record Payment (Only if unpaid balance exists and not cancelled) --}}
                                                @if($invoice->total_amount > $invoice->amount_paid && $invoice->status !== 'cancelled')
                                                <li>
                                                    <button class="dropdown-item text-success" onclick="openPaymentModal({{ $invoice->id }}, {{ $invoice->total_amount - $invoice->amount_paid }}, '{{ $invoice->invoice_number }}', {{ $invoice->customer->wallet_balance ?? 0 }})">
                                                        <i class="ri-money-dollar-circle-line align-bottom me-2 text-success"></i> Record Payment
                                                    </button>
                                                </li>
                                                @endif
                                            @endcan

                                            @can('manage deliveries')
                                                @if($invoice->status !== 'delivered' && $invoice->status !== 'cancelled')
                                                <li>
                                                    <button class="dropdown-item text-info" onclick="openDeliveryModal({{ $invoice->id }})">
                                                        <i class="ri-truck-line align-bottom me-2 text-info"></i> Mark Delivered
                                                    </button>
                                                </li>
                                                @endif
                                            @endcan

                                            @can('delete invoices')
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteInvoice({{ $invoice->id }})">
                                                    <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Delete
                                                </button>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No invoices found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals (Payment Modal) --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment - <span id="display_invoice_number"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Balance Due:</strong> <span id="display_balance_due"></span> KES
                </div>

                <ul class="nav nav-pills nav-custom nav-custom-light mb-3" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab">Manual Record</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-mpesa-tab" data-bs-toggle="pill" data-bs-target="#pills-mpesa" type="button" role="tab">M-Pesa STK Push</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-success" data-bs-toggle="pill" data-bs-target="#pills-wallet" type="button"><i class="ri-wallet-3-line me-1"></i> Pay from Wallet</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-finish-tab" data-bs-toggle="pill" data-bs-target="#finish-tab-content" type="button" role="tab" disabled>Status</button>
                    </li>
                </ul>

                <div class="tab-content" id="paymentTabsContent">
                    
                    <div class="tab-pane fade show active" id="pills-manual" role="tabpanel">
                        <form id="paymentForm">
                            <input type="hidden" id="pay_invoice_id" name="invoice_id">
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Payment Amount (KES) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                <span class="text-danger error-text amount_error"></span>
                            </div>

                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="mb-3">
                                <label for="method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="method" name="method" required>
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="M-Pesa">M-Pesa (Manual)</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                                <span class="text-danger error-text method_error"></span>
                            </div>

                            <div class="mb-3">
                                <label for="reference" class="form-label">Transaction Reference <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g. MPESA Code, Cheque No." required>
                                <span class="text-danger error-text reference_error"></span>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success" id="savePaymentBtn">Save Manual Payment</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pills-mpesa" role="tabpanel">
                        <form id="mpesaForm" class="row needs-validation" novalidate>
                            <input type="hidden" name="account_number" id="account_number"> 
                            <div class="col-md-7 mb-3">
                                <label for="mpesa_number" class="form-label">Mpesa Number</label>
                                <input type="text" class="form-control" name="mpesa_number" id="mpesa_number" placeholder="2547..." required>
                                <div class="invalid-feedback">Please enter a valid phone number.</div>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="mpesa_amount" class="form-label">Amount (KES)</label>
                                <input type="number" step="0.01" class="form-control" name="amount" id="mpesa_amount" required>
                                <div class="invalid-feedback">Enter Amount (KES).</div>
                            </div>
                            
                            <div class="col-12 text-end mt-2">
                                <button type="button" id="completeOrderButton" class="btn btn-primary btn-label right ms-auto">
                                    <i class="ri-smartphone-line label-icon align-middle fs-16 ms-2"></i> Send STK Prompt
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pills-wallet" role="tabpanel">
                        <form id="walletForm">
                            <input type="hidden" id="wallet_invoice_id">
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="ri-wallet-3-fill fs-24 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Customer Wallet Balance</h6>
                                    <h4 class="mb-0 fw-bold">KES <span id="display_wallet_balance">0.00</span></h4> 
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Amount to Deduct (KES) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="wallet_amount" required>
                            </div>
                            
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success" id="saveWalletBtn">Pay using Wallet</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="finish-tab-content" role="tabpanel">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deliveryForm">
                <div class="modal-header bg-info-subtle">
                    <h5 class="modal-title">Complete Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delivery_invoice_id">
                    <p class="mb-0">Mark this cake order as delivered / collected? The customer will be notified by SMS.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" id="saveDeliveryBtn">Confirm Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function sendReminder(id) {
        Swal.fire({
            title: 'Send Payment Reminder?',
            text: "This will immediately send an SMS to the customer with their balance and payment link.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f7b84b',
            cancelButtonColor: '#878a99',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait while the SMS is dispatched.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                axios.post(`/invoices/${id}/send-reminder`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire('Sent!', response.data.message ?? 'Reminder sent successfully.', 'success').then(() => {
                                location.reload(); // Refresh to update counter
                            });
                        } else {
                            Swal.fire('Failed', response.data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Could not send the reminder.', 'error');
                    });
            }
        });
    }

    function exportExcel() {
        const status = document.getElementById('status').value;
        const day = document.getElementById('occasion_date').value;
        let url = `{{ route('invoices.export') }}?status=${status}&occasion_date=${day}`;
        window.location.href = url;
    }

    function deleteInvoice(id) {
        Swal.fire({
            title: 'Delete Invoice?',
            text: "This action cannot be undone. Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/invoices/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success');
                        document.getElementById('row_' + id).remove();
                    })
                    .catch(error => {
                        let msg = error.response && error.response.data.message 
                                    ? error.response.data.message 
                                    : 'Could not delete invoice.';
                        
                        Swal.fire('Action Denied', msg, 'error');
                    });
            }
        });
    }

    // ==========================================
    // PAYMENT MODAL & MANUAL PAYMENT LOGIC
    // ==========================================
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    function openPaymentModal(invoiceId, balance, invoiceNumber, walletBalance = 0) {
        document.getElementById('paymentForm').reset();
        document.getElementById('mpesaForm').reset();
        document.getElementById('walletForm').reset(); // Reset wallet form
        $('.error-text').text(''); 
        
        resetStkBtn();

        // Default to Manual Tab
        var manualTab = new bootstrap.Tab(document.getElementById('pills-manual-tab'));
        manualTab.show();
        document.getElementById('pills-finish-tab').setAttribute('disabled', 'true');
        document.getElementById('finish-tab-content').innerHTML = '';

        // Populate standard details
        document.getElementById('display_invoice_number').innerText = invoiceNumber;
        document.getElementById('display_balance_due').innerText = parseFloat(balance).toFixed(2);
        
        // Manual form setup
        document.getElementById('pay_invoice_id').value = invoiceId;
        document.getElementById('amount').value = parseFloat(balance).toFixed(2);
        document.getElementById('amount').max = parseFloat(balance).toFixed(2);

        // STK form setup
        document.getElementById('account_number').value = invoiceNumber;
        document.getElementById('mpesa_amount').value = parseFloat(balance).toFixed(2);
        
        // --- Wallet form setup ---
        document.getElementById('wallet_invoice_id').value = invoiceId;
        document.getElementById('display_wallet_balance').innerText = parseFloat(walletBalance).toFixed(2);
        
        // Auto-fill wallet input with the balance due (or max out at their available wallet balance)
        let walletDeduct = Math.min(balance, walletBalance);
        document.getElementById('wallet_amount').value = parseFloat(walletDeduct).toFixed(2);
        document.getElementById('wallet_amount').max = parseFloat(walletBalance).toFixed(2);
        // ------------------------------

        paymentModal.show();
    }

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = {
            invoice_id: document.getElementById('pay_invoice_id').value,
            amount: document.getElementById('amount').value,
            method: document.getElementById('method').value,
            reference: document.getElementById('reference').value,
        };

        document.getElementById('savePaymentBtn').disabled = true;
        $('.error-text').text(''); 

        axios.post('/payments', formData)
        .then(response => {
            paymentModal.hide();
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
            document.getElementById('savePaymentBtn').disabled = false;
        });
    });

    function resetStkBtn() {
        const btn = document.getElementById('completeOrderButton');
        if(btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-smartphone-line label-icon align-middle fs-16 ms-2"></i> Send STK Prompt';
        }
    }

    // ==========================================
    // STK PUSH LOGIC
    // ==========================================
    document.getElementById('completeOrderButton').addEventListener('click', function() {
        const formElement = document.getElementById('mpesaForm');

        if (formElement) {
            if (!formElement.checkValidity()) {
                formElement.classList.add('was-validated');
                return;
            }

            const formData = new FormData(formElement);
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

            axios.post('{{ route('payments.topup.mpesa') }}', formData)
                .then(function(response) {
                    if (response.data.success) {
                        const finishTabButton = document.getElementById('pills-finish-tab');
                        finishTabButton.removeAttribute('disabled');
                        const finishTab = new bootstrap.Tab(finishTabButton);
                        finishTab.show();
                        
                        $('#finish-tab-content').html(`
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <lord-icon src="https://cdn.lordicon.com/rqptwppx.json" trigger="loop" colors="primary:#EB6D2F,secondary:#5A3733" style="width:120px;height:120px"></lord-icon>
                                </div>
                                <h5>Processing Payment...</h5>
                                <p class="text-muted">Please enter your M-Pesa PIN on your phone.</p>
                            </div>
                        `);

                        pollTransactionStatus(response.data.CheckoutRequestID, response.data.MerchantRequestID);
                    } else {
                        Swal.fire({
                            title: 'Failed',
                            html: response.data.message + (response.data.error ? `<br><b>Error:</b> ${response.data.error}` : ''),
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                        resetStkBtn();
                    }
                })
                .catch(function(error) {
                    Swal.fire('Error', 'An error occurred while processing your request.', 'error');
                    resetStkBtn();
                });
        }
    });

    function pollTransactionStatus(checkoutRequestId, merchantRequestId) {
        const interval = setInterval(function() {
            axios.post('{{ route('payments.transactionStatus') }}', {
                CheckoutRequestID: checkoutRequestId,
                MerchantRequestID: merchantRequestId,
            })
            .then(function(response) {
                if (response.data.success) {
                    const status = response.data.status;

                    if (status === 'confirmed') {
                        clearInterval(interval);
                        resetStkBtn();
                        
                        $('#finish-tab-content').html(`
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#198754,secondary:#198754" style="width:120px;height:120px"></lord-icon>
                                </div>
                                <h5>Payment Successful!</h5>
                                <p class="text-muted">The STK push was confirmed.</p>
                                <h3 class="fw-semibold">Receipt: <span class="text-success">${response.data.receipt_number}</span></h3>
                                <button class="btn btn-primary mt-3" onclick="location.reload()">Refresh Page</button>
                            </div>
                        `);
                    } else if (status !== 'pending') {
                        clearInterval(interval);
                        resetStkBtn();
                        
                        $('#finish-tab-content').html(`
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#dc3545,secondary:#dc3545" style="width:120px;height:120px"></lord-icon>
                                </div>
                                <h5>Transaction Failed or Cancelled.</h5>
                                <p class="text-danger">${response.data.message}</p>
                                <button class="btn btn-soft-danger mt-3" onclick="resetStkBtn(); new bootstrap.Tab(document.getElementById('pills-mpesa-tab')).show();">Try Again</button>
                            </div>
                        `);
                    }
                }
            })
            .catch(function(error) {
                console.error('Polling error:', error);
            });
        }, 5000); 
    }

    // ==========================================
    // WALLET PAYMENT LOGIC
    // ==========================================
    document.getElementById('walletForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = {
            invoice_id: document.getElementById('wallet_invoice_id').value,
            amount: document.getElementById('wallet_amount').value,
            method: 'Wallet', // Hardcode the method so the backend knows!
            reference: 'WALLET-PAY', // The backend will overwrite this with the timestamp
        };

        let btn = document.getElementById('saveWalletBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        axios.post('/payments', formData)
        .then(response => {
            paymentModal.hide();
            Swal.fire('Success', response.data.success, 'success').then(() => {
                location.reload(); 
            });
        })
        .catch(error => {
            if(error.response && error.response.status === 422) {
                // If the backend rejects it (e.g., insufficient funds)
                let msg = error.response.data.errors.amount 
                            ? error.response.data.errors.amount[0] 
                            : 'Validation failed.';
                Swal.fire('Error', msg, 'error');
            } else {
                Swal.fire('Error', 'Something went wrong!', 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Pay using Wallet';
        });
    });

    // ==========================================
    // DELIVERY MODAL LOGIC
    // ==========================================
    const deliveryModal = new bootstrap.Modal(document.getElementById('deliveryModal'));

    function openDeliveryModal(id) {
        document.getElementById('delivery_invoice_id').value = id;
        deliveryModal.show();
    }

    document.getElementById('deliveryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('delivery_invoice_id').value;
        
        document.getElementById('saveDeliveryBtn').disabled = true;
        document.getElementById('saveDeliveryBtn').innerHTML = 'Processing...';

        axios.put(`/invoices/${id}/deliver`, {})
            .then(response => {
                deliveryModal.hide();
                Swal.fire('Delivered!', response.data.success, 'success').then(() => {
                    location.reload(); 
                });
            })
            .catch(error => {
                Swal.fire('Error', 'Could not mark as delivered.', 'error');
                document.getElementById('saveDeliveryBtn').disabled = false;
                document.getElementById('saveDeliveryBtn').innerHTML = 'Confirm Delivery';
            });
    });
</script>
@endsection