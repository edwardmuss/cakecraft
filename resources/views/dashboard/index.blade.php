@extends('layouts.master')
@section('title') Dashboard @endsection 
@section('css')
    @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Dashboard @endslot
    @slot('title') Overview @endslot
@endcomponent

<div class="row">
    <h4 class="card-title mb-3 flex-grow-1">Viewing {{ date('M Y') }} Overview</h4>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Revenue</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalRevenue }}">{{ number_format($totalRevenue, 2) }}</span> <small class="text-muted fs-13">KES</small></h4>
                        <a href="{{ route('invoices.index') }}" class="text-decoration-underline text-muted">View all payments</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                            <i class="ri-wallet-3-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Outstanding Balance</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-danger"><span class="counter-value" data-target="{{ $outstandingBalance }}">{{ number_format($outstandingBalance, 2) }}</span> <small class="text-muted fs-13">KES</small></h4>
                        <a href="{{ route('invoices.index', ['status' => 'processing']) }}" class="text-decoration-underline text-muted">View unpaid invoices</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle text-danger rounded fs-3">
                            <i class="ri-file-warning-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Customers</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalCustomers }}">{{ $totalCustomers }}</span></h4>
                        <a href="{{ route('customers.index') }}" class="text-decoration-underline text-muted">View all customers</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                            <i class="ri-team-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Invoices</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalInvoices }}">{{ $totalInvoices }}</span></h4>
                        <a href="{{ route('invoices.create') }}" class="text-decoration-underline text-muted">Create new invoice</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                            <i class="ri-file-list-3-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 d-none">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Revenue Overview ({{ date('Y') }})</h4>
            </div>
            <div class="card-body p-0 pb-2">
                <div class="w-100">
                    <div id="revenue_chart_1" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                {{-- <h4 class="card-title mb-0 flex-grow-1">Sales by Communion Week ({{ $currentMonthName }})</h4> --}}
                <h4 class="card-title mb-0 flex-grow-1">Sales by Communion Week (Last 12 Weeks)</h4>
            </div>
            <div class="card-body p-0 pb-2">
                <div class="w-100">
                    <div id="revenue_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Invoice Status</h4>
            </div>
            <div class="card-body">
                <div id="status_chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>
</div>

@if($overpaidInvoices->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                <h5 class="card-title text-warning-emphasis mb-0"><i class="ri-error-warning-line me-1"></i> Overpaid Invoices Detected</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Total Billed</th>
                                <th>Amount Paid (C2B)</th>
                                <th class="text-success">Excess Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overpaidInvoices as $overpaid)
                                @php $excess = $overpaid->amount_paid - $overpaid->total_amount; @endphp
                                <tr>
                                    <td><a href="{{ route('invoices.show', $overpaid->id) }}">#{{ $overpaid->invoice_number }}</a></td>
                                    <td>{{ $overpaid->customer->organization_name ?? $overpaid->customer->name }}</td>
                                    <td>KES {{ number_format($overpaid->total_amount, 2) }}</td>
                                    <td>KES {{ number_format($overpaid->amount_paid, 2) }}</td>
                                    <td class="text-success fw-bold">+KES {{ number_format($excess, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success transfer-wallet-btn" data-id="{{ $overpaid->id }}" data-excess="{{ $excess }}">
                                            <i class="ri-wallet-3-line"></i> Move to Wallet
                                        </button>
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
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex border-bottom-dashed">
                <h4 class="card-title mb-0 flex-grow-1">Action Required: Overdue Invoices</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Balance (KES)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdueInvoices as $invoice)
                                @php
                                    $daysOverdue = \Carbon\Carbon::parse($invoice->due_date)->diffInDays(today());
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="fw-medium link-primary">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">{{ $invoice->customer->name ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td><span class="text-danger">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</span></td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger">{{ $daysOverdue }} days</span>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ number_format($invoice->total_amount - $invoice->amount_paid, 2) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-soft-secondary">View Details</a>
                                        {{-- Send Reminder (Only if unpaid balance exists and not cancelled) --}}
                                            @can('view financials')
                                                @if($invoice->total_amount > $invoice->amount_paid && $invoice->status !== 'cancelled')
                                                    <button class="btn btn-sm btn-soft-primary" onclick="sendReminder({{ $invoice->id }})">
                                                        <i class="ri-notification-3-line me-2"></i> 
                                                        Send Reminder {{ $invoice->reminder_count > 0 ? '(' . $invoice->reminder_count . ')' : '' }}
                                                    </button>
                                                @endif
                                            @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:72px;height:72px"></lord-icon>
                                        <h5 class="mt-4">Excellent work!</h5>
                                        <p class="text-muted">You have no overdue invoices at the moment.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $overdueInvoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

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

document.addEventListener("DOMContentLoaded", function() {

    
    // ==========================================
    // 1. REVENUE AREA CHART
    // ==========================================
    var revenueData = @json($monthlyRevenueData);
    
    var revenueChartOptions = {
        series: [{
            name: "Revenue (KES)",
            data: revenueData
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
        },
        colors: ['#0ab39c'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            },
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "KES " + value.toLocaleString();
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "KES " + val.toLocaleString();
                }
            }
        }
    };

    // var revenueChart = new ApexCharts(document.querySelector("#revenue_chart"), revenueChartOptions);
    // revenueChart.render();

    // ==========================================
    // 1. WEEKLY SALES BAR CHART
    // ==========================================
    var weeklySalesData = @json($weeklySalesData);
    
    var revenueChartOptions = {
        series: [{
            name: "Sales (KES)",
            data: weeklySalesData
        }],
        chart: {
            height: 350,
            type: 'bar', // Changed from 'area' to 'bar'
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: false, // Set to true if they want the bars going left-to-right
                columnWidth: '45%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val > 0 ? "KES " + val.toLocaleString() : "";
            },
            style: {
                fontSize: '10px',
                colors: ["#304758"]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#3577f1', '#f06548', '#878a99', '#f7b84b', '#6559cc'], // Multiple colors like their Excel
        xaxis: {
            categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
        },
        yaxis: {
            title: {
                text: 'Sales (KES)'
            },
            labels: {
                formatter: function (value) {
                    return "KES " + value.toLocaleString();
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "KES " + val.toLocaleString();
                }
            }
        }
    };

    // var revenueChart = new ApexCharts(document.querySelector("#revenue_chart"), revenueChartOptions);
    // revenueChart.render();

    // ==========================================
    // 1. 12-WEEK ROLLING SALES BAR CHART
    // ==========================================
    var weeklySalesData = @json($weeklySalesData);
    var chartLabels = {!! $chartLabelsJson !!}; // Dynamic labels from controller
    
    var revenueChartOptions = {
        series: [{
            name: "Sales (KES)",
            data: weeklySalesData
        }],
        chart: {
            height: 350,
            type: 'bar', // Clean Bar Chart!
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: false, 
                columnWidth: '60%',
                borderRadius: 4 // Slightly rounded corners for a modern look
            },
        },
        dataLabels: {
            enabled: false // Turned off so the bars don't look cluttered when there are 12 of them
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#0ab39c'], // Primary brand color
        xaxis: {
            categories: chartLabels, // Uses "Mar W1", "Mar W2", etc.
            labels: {
                rotate: -45, // Rotates labels slightly so they fit perfectly
                style: {
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Sales Amount (KES)'
            },
            labels: {
                formatter: function (value) {
                    return "KES " + value.toLocaleString();
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "KES " + val.toLocaleString();
                }
            }
        }
    };

    var revenueChart = new ApexCharts(document.querySelector("#revenue_chart"), revenueChartOptions);
    revenueChart.render();

    // ==========================================
    // 2. INVOICE STATUS DONUT CHART
    // ==========================================
    var chartStatuses = @json($chartStatuses);
    
    var donutChartOptions = {
        series: [
            chartStatuses['Processing'], 
            chartStatuses['Partially Paid'], 
            chartStatuses['Paid'], 
            chartStatuses['Packed'],
            chartStatuses['Dispatched'],
            chartStatuses['Delivered'], 
            chartStatuses['Cancelled']
        ],
        labels: ['Processing', 'Partially Paid', 'Paid', 'Packed', 'Dispatched', 'Delivered', 'Cancelled'],
        chart: {
            type: 'donut',
            height: 333
        },
        // Colors: Red, Yellow, Green, Purple, Cyan, Blue, Grey
        colors: ['#f06548', '#f7b84b', '#0ab39c', '#6559cc', '#299cdb', '#3577f1', '#878a99'], 
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom'
        },
        stroke: { show: false }
    };

    var donutChart = new ApexCharts(document.querySelector("#status_chart"), donutChartOptions);
    donutChart.render();

    // --- Transfer Overpayment to Wallet ---
    $(document).on('click', '.transfer-wallet-btn', function() {
        let invoiceId = $(this).data('id');
        let excessAmount = parseFloat($(this).data('excess')).toFixed(2);

        Swal.fire({
            title: 'Move to Wallet?',
            text: `Transfer KES ${excessAmount} to this customer's wallet?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754', // Success green
            cancelButtonColor: '#878a99',
            confirmButtonText: 'Yes, transfer it!'
        }).then((result) => {
            if (result.isConfirmed) {
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating ledgers and wallet balance.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                axios.post(`/invoices/${invoiceId}/transfer-overpayment`)
                    .then(response => {
                        Swal.fire('Transferred!', response.data.success, 'success').then(() => {
                            location.reload(); // Reload to clear it from the table
                        });
                    })
                    .catch(error => {
                        let msg = error.response && error.response.data.message 
                                    ? error.response.data.message 
                                    : 'Transfer failed.';
                        Swal.fire('Error', msg, 'error');
                    });
            }
        });
    });
});
</script>
@endsection