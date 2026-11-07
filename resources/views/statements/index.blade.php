@extends('layouts.master')
@section('title') Customer Statements @endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Reports @endslot
    @slot('title') Customer Statements @endslot
@endcomponent

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-none border">
            <div class="card-body">
                <form action="{{ route('statements.index') }}" method="GET" id="statementForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select select2" required>
                                <option value="">Choose Customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->organization_name ?? $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-t')) }}" required>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="ri-file-text-line me-1"></i> View</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statement Results -->
@if($statement)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Statement of Account</h5>
                    <p class="text-muted mb-0">
                        {{ $statement['customer']->organization_name ?? $statement['customer']->name }} | 
                        Period: {{ $statement['start_date']->format('d M Y') }} to {{ $statement['end_date']->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('statements.pdf', request()->all()) }}" target="_blank" class="btn btn-danger">
                        <i class="ri-file-pdf-line align-bottom me-1"></i> Download PDF
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Details</th>
                                <th>Reference</th>
                                <th class="text-end">Charges (KES)</th>
                                <th class="text-end">Payments (KES)</th>
                                <th class="text-end">Balance (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance Row -->
                            <tr class="table-active">
                                <td colspan="5" class="text-end fw-semibold">Opening Balance</td>
                                <td class="text-end fw-semibold">{{ number_format($statement['opening_balance'], 2) }}</td>
                            </tr>

                            <!-- Transactions -->
                            @forelse($statement['transactions'] as $tx)
                            <tr>
                                <td>{{ $tx['date']->format('d M Y') }}</td>
                                <td>
                                    @if($tx['type'] == 'Invoice')
                                        <span class="badge bg-primary-subtle text-primary mb-1">Invoice</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success mb-1">Payment</span>
                                    @endif
                                    <br>{{ $tx['description'] }}
                                </td>
                                <td class="fw-medium">{{ $tx['reference'] }}</td>
                                <td class="text-end text-danger">{{ $tx['debit'] > 0 ? number_format($tx['debit'], 2) : '-' }}</td>
                                <td class="text-end text-success">{{ $tx['credit'] > 0 ? number_format($tx['credit'], 2) : '-' }}</td>
                                <td class="text-end fw-medium">{{ number_format($tx['balance'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center p-4 text-muted">No transactions found in this date range.</td>
                            </tr>
                            @endforelse

                            <!-- Closing Balance / Totals Row -->
                            <tr class="table-dark">
                                <td colspan="3" class="text-end fw-bold">Totals & Closing Balance</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($statement['total_debit'], 2) }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($statement['total_credit'], 2) }}</td>
                                <td class="text-end fw-bold fs-15">{{ number_format($statement['closing_balance'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <h6 class="text-muted mb-2">Total Billed</h6>
                        <h5 class="text-danger mb-0">KES {{ number_format($statement['total_debit'], 2) }}</h5>
                    </div>
                    <div class="col-4 border-end">
                        <h6 class="text-muted mb-2">Total Paid</h6>
                        <h5 class="text-success mb-0">KES {{ number_format($statement['total_credit'], 2) }}</h5>
                    </div>
                    <div class="col-4">
                        <h6 class="text-muted mb-2">Amount Owed</h6>
                        <h5 class="text-primary mb-0">KES {{ number_format($statement['closing_balance'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection