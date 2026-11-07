@extends('layouts.master')
@section('title') Analytics & Reports @endsection 
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Reports @endslot
    @slot('title') Logistics & Sales Overview @endslot
@endcomponent

{{-- Filter Section --}}
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card shadow-none border">
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="customer_id" class="form-label">Church / Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select2">
                                <option value="">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->organization_name ?? $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="communion_month" class="form-label">Communion Month</label>
                            <input type="month" name="communion_month" id="communion_month" class="form-control" value="{{ request('communion_month', date('Y-m')) }}">
                        </div>

                        <div class="col-md-2"></div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-filter-2-line me-1"></i> Generate
                            </button>
                            
                            {{-- This submits the exact same form but passes the export_excel=1 flag --}}
                            <button type="submit" name="export_excel" value="1" class="btn btn-success w-100">
                                <i class="ri-file-excel-2-line me-1"></i> Export
                            </button>
                            
                            <a href="{{ route('reports.index') }}" class="btn btn-soft-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Metric Cards --}}
<div class="row">
    <div class="col-xl col-md-4 col-sm-6 mb-3">
        <div class="card card-animate border-top border-top-success border-3 h-100">
            <div class="card-body text-center">
                <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-success">
                    KES {{ number_format($totalRevenue, 2) }}
                </h4>
                <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Billed Revenue</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl col-md-4 col-sm-6 mb-3">
        <div class="card card-animate border-top border-top-warning border-3 h-100">
            <div class="card-body text-center">
                <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-warning">
                    KES {{ number_format($outstandingBalance, 2) }}
                </h4>
                <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Unpaid Balance</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-4 col-sm-6 mb-3">
        <div class="card card-animate border-top border-top-primary border-3 h-100">
            <div class="card-body text-center">
                <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-primary">
                    {{ number_format($totalDeliveredTrays) }}
                </h4>
                <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Trays Delivered</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-6 col-sm-6 mb-3">
        <div class="card card-animate border-top border-top-info border-3 h-100">
            <div class="card-body text-center">
                <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-info">
                    {{ number_format($totalReturnedTrays) }}
                </h4>
                <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Empties Picked Up</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-6 col-sm-12 mb-3">
        <div class="card card-animate border-top border-top-danger border-3 h-100">
            <div class="card-body text-center">
                <h4 class="fs-22 fw-semibold ff-secondary mb-2 {{ $missingTrays > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($missingTrays) }}
                </h4>
                <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Missing Trays</p>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0">Detailed Logistics Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Month/Week</th>
                                <th>Invoice #</th>
                                <th>Church / Customer</th>
                                <th>Status</th>
                                <th class="text-center">Trays Delivered</th>
                                <th class="text-center">Trays Returned</th>
                                <th class="text-center">Variance (Lost)</th>
                                <th class="text-end">Total Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                @php
                                    $invoiceDeliveredTrays = $invoice->items->sum('tray_count');
                                    $invoiceMissing = $invoiceDeliveredTrays - $invoice->returned_trays;
                                @endphp
                                <tr>
                                    <td>
                                        {{-- Shows new Month/Week format, falls back to legacy dates if older records --}}
                                        @if($invoice->communion_month)
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($invoice->communion_month)->format('M Y') }}</span> 
                                            <span class="text-muted text-nowrap">• Week {{ $invoice->communion_week }}</span>
                                        @else
                                            {{ $invoice->communion_date ? \Carbon\Carbon::parse($invoice->communion_date)->format('d M, Y') : 'N/A' }}
                                        @endif
                                    </td>
                                    <td><a href="{{ route('invoices.show', $invoice->id) }}" class="fw-bold">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->customer->organization_name ?? $invoice->customer->name }}</td>
                                    <td>
                                        @if($invoice->status == 'delivered')
                                            <span class="badge bg-success-subtle text-success">Delivered</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ $invoiceDeliveredTrays }}</td>
                                    <td class="text-center text-primary fw-bold">{{ $invoice->returned_trays }}</td>
                                    <td class="text-center">
                                        @if($invoice->status == 'delivered')
                                            @if($invoiceMissing > 0)
                                                <span class="badge bg-danger">{{ $invoiceMissing }} Missing</span>
                                            @else
                                                <span class="badge bg-success">All Returned</span>
                                            @endif
                                        @else
                                            <span class="text-muted fs-12">Pending Return</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No records found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- {{ $invoices->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('build/js/app.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endsection