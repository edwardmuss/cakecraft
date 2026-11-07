@extends('layouts.master')
@section('title') All Payments @endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Financials @endslot
    @slot('title') All Payments @endslot
@endcomponent

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card bg-light border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('payments.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Ref, Invoice #, or Customer..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control select2" name="customer_id">
                                <option value="">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->organization_name ?? $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="method">
                                <option value="">All Methods</option>
                                <option value="Cash" {{ request('method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="M-Pesa" {{ request('method') == 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="Bank Transfer" {{ request('method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cheque" {{ request('method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" name="filter" id="dateFilter" onchange="toggleCustomDates()">
                                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="this_week" {{ request('filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="last_30_days" {{ request('filter', 'last_30_days') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="this_year" {{ request('filter') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }}>Custom Date</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-2-line me-1"></i> Filter</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-soft-secondary w-100">Clear</a>
                        </div>
                    </div>

                    <div class="row g-3 mt-1 custom-date-group {{ request('filter') == 'custom' ? '' : 'd-none' }}">
                        <div class="col-md-12 d-flex gap-2 align-items-center justify-content-end">
                            <label class="form-label mb-0 fw-bold">From:</label>
                            <input type="date" class="form-control w-auto" name="start_date" id="start_date" value="{{ request('start_date') }}">
                            <label class="form-label mb-0 fw-bold">To:</label>
                            <input type="date" class="form-control w-auto" name="end_date" id="end_date" value="{{ request('end_date') }}">
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
                <h5 class="card-title mb-0">Transaction History</h5>
                <button type="button" onclick="document.getElementById('filterForm').action='{{ route('payments.export') }}'; document.getElementById('filterForm').submit(); document.getElementById('filterForm').action='{{ route('payments.index') }}';" class="btn btn-success btn-sm">
                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Transaction Ref</th>
                                <th>Invoice Number</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th>Amount (KES)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                                <td><span class="fw-medium">{{ $payment->reference }}</span></td>
                                <td>
                                    @if($payment->invoice)
                                        <a href="{{ route('invoices.show', $payment->invoice->id) }}" class="link-primary fw-medium">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $payment->invoice->customer->organization_name ?? $payment->invoice->customer->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $payment->method }}</span>
                                </td>
                                <td><span class="fw-semibold text-success">{{ number_format($payment->amount, 2) }}</span></td>
                                <td>
                                    <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-sm btn-soft-secondary">
                                        <i class="ri-download-2-line"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-4">No payments found matching your criteria.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    function toggleCustomDates() {
        let filter = document.getElementById('dateFilter').value;
        let customGroups = document.querySelectorAll('.custom-date-group');
        let startInput = document.getElementById('start_date');
        let endInput = document.getElementById('end_date');

        if (filter === 'custom') {
            customGroups.forEach(el => el.classList.remove('d-none'));
            startInput.setAttribute('required', 'required');
            endInput.setAttribute('required', 'required');
        } else {
            customGroups.forEach(el => el.classList.add('d-none'));
            startInput.removeAttribute('required');
            endInput.removeAttribute('required');
            startInput.value = '';
            endInput.value = '';
        }
    }
</script>
@endsection