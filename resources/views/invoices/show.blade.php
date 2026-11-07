@extends('layouts.master')
@section('title') Invoice Details @endsection 
@section('css')
{{--  --}}
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Invoices @endslot
    @slot('title') Details @endslot
@endcomponent


<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Invoice {{ $invoice->invoice_number }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
                    <p class="mb-1"><strong>Organization:</strong> {{ $invoice->customer->organization_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                    
                    <p class="mb-1"><strong>Cake needed:</strong>
                        @if($invoice->occasion_date)
                            <span class="badge bg-primary-subtle text-primary">{{ \Carbon\Carbon::parse($invoice->occasion_date)->format('D, d M Y') }}</span>
                        @else N/A @endif
                    </p>
                    @if($invoice->occasion_type)
                    <p class="mb-1"><strong>Occasion:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->occasion_type)) }}{{ $invoice->recipient_name ? ' for ' . $invoice->recipient_name : '' }}</p>
                    @endif
                    <p class="mb-1"><strong>Fulfilment:</strong> {{ ucfirst($invoice->fulfilment_type ?? 'pickup') }}{{ $invoice->delivery_address ? ' - ' . $invoice->delivery_address : '' }}</p>

                    @can('manage deliveries')
                    <p class="mb-1 mt-2 text-info"><strong><i class="ri-map-pin-line"></i> Delivery Location:</strong><br>
                        {{ $invoice->customer->delivery_location ?? 'No location provided' }}
                    </p>
                    @endcan
                </div>

                {{-- Hide Financials from Logistics/Packers --}}
                @can('view financials')
                <div class="border-top border-bottom py-3 mb-3">
                    @if($invoice->shipping_fee > 0)
                        <p class="mb-1 text-muted">Subtotal: {{ number_format($invoice->subtotal, 2) }} KES</p>
                        <p class="mb-1 text-muted">Tax: {{ number_format($invoice->tax_amount, 2) }} KES</p>
                        <p class="mb-1 text-muted">Shipping: {{ number_format($invoice->shipping_fee, 2) }} KES</p>
                    @endif
                    <p class="mb-1"><strong>Grand Total:</strong> {{ number_format($invoice->total_amount, 2) }} KES</p>
                    <p class="mb-1"><strong>Paid:</strong> <span class="text-success">{{ number_format($invoice->amount_paid, 2) }} KES</span></p>
                    <p class="mb-0 fs-15 mt-2 pt-2 border-top"><strong>Balance:</strong> <span class="text-danger fw-bold">{{ number_format($invoice->total_amount - $invoice->amount_paid, 2) }} KES</span></p>
                </div>
                @endcan

                <div class="mb-4">
                    <strong>Current Status: </strong>
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
                </div>

                <div class="d-flex flex-column gap-2 mt-3">
                    @can('update invoices')
                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-primary w-100">
                            <i class="ri-pencil-line align-bottom me-1"></i> Edit Invoice Data
                        </a>
                    @endcan

                    {{-- Quick Action for Packers --}}
                    @can('pack orders')
                        @if($invoice->status === 'processing' || $invoice->status === 'paid' || $invoice->status === 'partially_paid')
                        <button class="btn btn-secondary w-100" onclick="updateStatus({{ $invoice->id }}, 'packed')">
                            <i class="ri-box-3-line align-bottom me-1"></i> Mark as Packed
                        </button>
                        @endif
                    @endcan

                    {{-- Quick Action for Drivers (Dispatch) --}}
                    @can('manage deliveries')
                        @if($invoice->status === 'packed')
                        <button class="btn btn-dark w-100" onclick="updateStatus({{ $invoice->id }}, 'dispatched')">
                            <i class="ri-flight-takeoff-line align-bottom me-1"></i> Mark as Dispatched
                        </button>
                        @endif
                    @endcan

                    {{-- Quick Action for Drivers (Deliver) --}}
                    @can('manage deliveries')
                        @if($invoice->status !== 'delivered')
                        <button class="btn btn-info w-100" onclick="openDeliveryModal({{ $invoice->id }})">
                            <i class="ri-truck-line align-bottom me-1"></i> Mark Delivered / Collected
                        </button>
                        @endif
                    @endcan

                    @can('view financials')
                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-soft-primary w-100">
                            <i class="ri-printer-line align-bottom me-1"></i> Print PDF
                        </a>
                    @endcan

                    {{-- Send Reminder (Only if unpaid balance exists and not cancelled) --}}
                    @can('view financials')
                        @if($invoice->total_amount > $invoice->amount_paid && $invoice->status !== 'cancelled')
                        <li>
                            <button class="btn btn-success w-100" onclick="sendReminder({{ $invoice->id }})">
                                <i class="ri-notification-3-line align-bottom me-2 text-warning"></i> 
                                Send Reminder {{ $invoice->reminder_count > 0 ? '(' . $invoice->reminder_count . ')' : '' }}
                            </button>
                        </li>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Ordered Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cake</th>
                                <th>Qty</th>
                                <th>Details</th>
                                @can('view financials')
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td><strong>{{ $item->quantity }}</strong></td>
                                <td>
                                    @php $d = collect([$item->size_label, $item->flavour_name])->filter()->implode(' · '); @endphp
                                    @if($d)<span class="badge bg-primary-subtle text-primary fs-12">{{ $d }}</span>@endif
                                    @if($item->cake_message)<div class="fs-12 fst-italic mt-1">"{{ $item->cake_message }}"</div>@endif
                                    @if(!$d && !$item->cake_message)<span class="text-muted fs-12">-</span>@endif
                                </td>
                                @can('view financials')
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                        
                        {{-- Breakdown Footer for Financials --}}
                        @can('view financials')
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Subtotal:</td>
                                <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">VAT ({{ $invoice->branch->vat_percentage ?? 0 }}%):</td>
                                <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @if($invoice->shipping_fee > 0)
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Shipping Fee:</td>
                                <td class="text-end">{{ number_format($invoice->shipping_fee, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end fw-bold fs-14">Grand Total:</td>
                                <td class="text-end fw-bold fs-14">{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endcan
                    </table>
                </div>
            </div>
        </div>

        @can('view financials')
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">Payments Received</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Ref</th>
                                <th>Amount (KES)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                                <td>{{ $payment->method }}</td>
                                <td>{{ $payment->reference }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @can('update payments')
                                        <button class="btn btn-sm btn-soft-primary" onclick="editPayment({{ $payment->id }})">Edit</button>
                                    @endcan
                                    @can('delete payments')
                                        <button class="btn btn-sm btn-soft-danger" onclick="deletePayment({{ $payment->id }})">Delete</button>
                                    @endcan
                                    <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-sm btn-soft-success">Receipt</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>

<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPaymentForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_payment_id">
                    
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Payment Amount (KES) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                        <span class="text-danger error-text amount_error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="edit_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_method" name="method" required>
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                        <span class="text-danger error-text method_error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="edit_reference" class="form-label">Transaction Reference <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_reference" name="reference" required>
                        <span class="text-danger error-text reference_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updatePaymentBtn">Update Payment</button>                    
                </div>
            </form>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const editPaymentModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));

    function editPayment(id) {
        $('.error-text').text('');
        
        axios.get(`/payments/${id}/edit`)
            .then(response => {
                const payment = response.data;
                document.getElementById('edit_payment_id').value = payment.id;
                document.getElementById('edit_amount').value = payment.amount;
                document.getElementById('edit_method').value = payment.method;
                document.getElementById('edit_reference').value = payment.reference;
                
                editPaymentModal.show();
            })
            .catch(error => {
                Swal.fire('Error', 'Could not fetch payment data', 'error');
            });
    }

    function deletePayment(id) {
        Swal.fire({
            title: 'Delete Payment?',
            text: "This action cannot be undone. The invoice balance will be recalculated.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                axios.delete(`/payments/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success').then(() => {
                            location.reload(); // Reload the page to update the invoice balance UI
                        });
                    })
                    .catch(error => {
                        let msg = error.response && error.response.data.message 
                                    ? error.response.data.message 
                                    : 'Could not delete payment.';
                        
                        Swal.fire('Action Denied', msg, 'error');
                    });
            }
        });
    }

    document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('edit_payment_id').value;
        let formData = {
            amount: document.getElementById('edit_amount').value,
            method: document.getElementById('edit_method').value,
            reference: document.getElementById('edit_reference').value,
        };

        document.getElementById('updatePaymentBtn').disabled = true;
        $('.error-text').text(''); 

        axios.put(`/payments/${id}`, formData)
            .then(response => {
                editPaymentModal.hide();
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
                document.getElementById('updatePaymentBtn').disabled = false;
            });
    });

    // --- Universal Status Updater for Packers/Drivers ---
    function updateStatus(id, newStatus) {
        Swal.fire({
            title: `Mark as ${newStatus}?`,
            text: "This will update the progress of the order.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#405189',
            cancelButtonColor: '#f06548',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/invoices/${id}/update-status`, { status: newStatus })
                    .then(response => {
                        Swal.fire('Updated!', 'The order status has been changed.', 'success').then(() => {
                            location.reload(); 
                        });
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Could not update status.', 'error');
                    });
            }
        });
    }

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
                                location.reload(); // Reload to update the reminder counter on the button!
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