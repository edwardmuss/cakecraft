@extends('layouts.master')
@section('title') @lang('translation.dashboards') @endsection
@section('css')
<link href="{{ asset('build/libs/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('build/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<div class="row">
    <div class="col">

        <div class="h-100">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">{{ greetBasedOnTime() }}, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted mb-0">Here's what's happening with <span class="text-primary fw-bold">{{ $current_account->branch_name }}</span> today. </p>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <form action="javascript:void(0);">
                                <div class="row g-3 mb-0 align-items-center">
                                    <div class="col-sm-auto">
                                        <div class="input-group">
                                            <input type="text" class="form-control border dash-filter-picker" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                            <div class="input-group-text bg-primary border-primary text-white">
                                                <i class="ri-calendar-2-line"></i></div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-soft-primary"><i class="ri-add-circle-line align-middle me-1"></i>
                                            Add Product</button>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-soft-secondary btn-icon waves-effect waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <!--end row-->

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Total Sent SMS</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                        {{ $sentPercentage }} %
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ formatAmount($sentCount)['amount'] }}">0</span>{{ formatAmount($sentCount)['suffix'] }}
                                    </h4>
                                    <a href="{{ route('messages.outbox') }}" class="link-secondary text-decoration-underline">TODAY</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-send text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Delivered SMS</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                        {{ $deliveredPercentage  }}%
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ formatAmount($deliveredCount)['amount'] }}">0</span>{{ formatAmount($deliveredCount)['suffix'] }}</h4>
                                    <a href="{{ route('messages.outbox') }}" class="link-secondary text-decoration-underline">TODAY</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-message-rounded-check text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Scheduled SMS</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                        {{ $scheduledPercentage }} % (Today)
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ formatAmount($scheduledCount)['amount'] }}">0</span>{{ formatAmount($scheduledCount)['suffix'] }}
                                    </h4>
                                    <a href="{{ route('messages.outbox') }}" class="link-secondary text-decoration-underline">ALL TIME</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-time-five text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Blocked SMS</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-danger fs-14 mb-0">
                                        <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                                        {{ $blockedPercentage }} %
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ formatAmount($blockedCount)['amount'] }}">0</span>{{ formatAmount($blockedCount)['suffix'] }}
                                    </h4>
                                    <a href="{{ route('messages.outbox') }}" class="link-secondary text-decoration-underline">TODAY</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-block text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div> <!-- end row-->

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Account Analytics</h4>
                            <div class="d-none">
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    ALL
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    1M
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    6M
                                </button>
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    1Y
                                </button>
                            </div>
                        </div><!-- end card header -->

                        <div class="card-header p-0 border-0 bg-light-subtle">
                            <div class="row g-0 text-center">
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value" data-target="{{ $accountBalance }}">0</span></h5>
                                        <p class="text-muted mb-0">Balance (Units)</p>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value" data-target="{{ $shortcodeCount }}">0</span></h5>
                                        <p class="text-muted mb-0">Shortcodes</p>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value" data-target="{{ $userCount }}">0</span></h5>
                                        <p class="text-muted mb-0">Users</p>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0 border-end-0">
                                        <h5 class="mb-1 text-primary"><span class="counter-value" data-target="{{ formatAmount($contactCount)['amount'] }}">0</span>{{ formatAmount($contactCount)['suffix'] }}</h5>
                                        <p class="text-muted mb-0">Contacts</p>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                        </div><!-- end card header -->

                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <div id="sms_report_charts" data-colors='["--vz-primary", "--vz-light", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-4">
                    <!-- card -->
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Sales by Locations</h4>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    Export Report
                                </button>
                            </div>
                        </div><!-- end card header -->

                        <!-- card body -->
                        <div class="card-body">

                            <div id="sales-by-locations" data-colors='["--vz-light", "--vz-secondary", "--vz-primary"]' style="height: 269px" dir="ltr"></div>

                            <div class="px-2 py-2 mt-1">
                                <p class="mb-1">Canada <span class="float-end">75%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="75">
                                    </div>
                                </div>

                                <p class="mt-3 mb-1">Greenland <span class="float-end">47%</span>
                                </p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="47">
                                    </div>
                                </div>

                                <p class="mt-3 mb-1">Russia <span class="float-end">82%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 82%" aria-valuenow="82" aria-valuemin="0" aria-valuemax="82">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <div class="row">
                <div class="col-xl-4">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Messages by Network</h4>
                            {{-- <div class="flex-shrink-0">
                                <div class="dropdown card-header-dropdown">
                                    <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Download Report</a>
                                        <a class="dropdown-item" href="#">Export</a>
                                        <a class="dropdown-item" href="#">Import</a>
                                    </div>
                                </div>
                            </div> --}}
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div id="network-count-pie" data-colors='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.70", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.45"]' class="apex-charts" dir="ltr"></div>
                        </div>
                    </div> <!-- .card-->
                </div> <!-- .col-->

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Recent Payments</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('payments.index') }}" class="btn btn-soft-primary btn-sm">
                                    <i class="ri-file-list-3-line align-middle"></i> View All
                                </a>
                            </div>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Reference</th>
                                            <th>Method</th>
                                            <th>Amount (KES)</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($latestPayments as $index => $payment)
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>
                                                <a href="{{ route('payments.showInvoice', $payment->id) }}" class="fw-medium link-primary">#RT-{{ $payment->reference }}</a>
                                            </td>
                                            <td>{{ $payment->method }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>
                                            <td>
                                                @if($payment->status == 'paid')
                                                    <span class="badge bg-success-subtle text-success text-uppercase text-uppercase">{{ $payment->status }}</span>
                                                @elseif($payment->status == 'unpaid')
                                                    <span class="badge bg-danger-subtle text-danger text-uppercase">{{ $payment->status }}</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success text-uppercase">{{ $payment->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y h:i A') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('payments.showInvoice', $payment->id) }}" class="btn btn-sm btn-soft-info"><i class="ri-eye-line me-2"></i>View</a>
                                                </div>
                                            </td>
                                        </tr><!-- end tr -->
                                        @endforeach
                                    </tbody><!-- end tbody -->
                                </table><!-- end table -->
                            </div>
                        </div>
                    </div> <!-- .card-->
                </div> <!-- .col-->
            </div> <!-- end row-->

        </div> <!-- end .h-100-->

    </div> <!-- end col -->

    <div class="col-auto layout-rightside-col">
        <div class="overlay"></div>
        <div class="layout-rightside">
            <div class="card h-100 rounded-0">
                <div class="card-body p-0">
                    <div class="p-3">
                        <h6 class="text-muted mb-0 text-uppercase fw-semibold">Recent Activity</h6>
                    </div>
                    <div data-simplebar style="max-height: 410px;" class="p-3 pt-0">
                        <div class="acitivity-timeline acitivity-main">
                            @forelse($activityLogs as $index => $log)
                            <div class="acitivity-item d-flex {{ $index > 0 ? 'py-3' : '' }}">
                                <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                        <i class="{{ $log->action == 'User Login' ? 'ri-login-circle-line' : 'ri-notification-3-line' }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $log->action }}</h6>
                                    <p class="text-muted mb-1">{{ $log->description }} </p>
                                    <small class="mb-0 text-muted">{{ $log->created_at->diffForHumans() }} by <strong>{{ $log->user ? $log->user->name : 'System' }}</strong></small>
                                </div>
                            </div>
                            @empty
                                <p class="text-muted mb-1">No recent activity</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="p-3 mt-2">
                        <h6 class="text-muted mb-3 text-uppercase fw-semibold">Contacts Per Network
                        </h6>

                        <ol class="ps-3 text-muted">
                            @forelse($operators as $operator)
                                <li class="py-1">
                                    <a href="javascript:void(0);" class="text-muted">{{ ucfirst($operator->operator) }} <span class="float-end">({{ $operator->count }})</span></a>
                                </li>
                            @empty
                                <li class="py-1">
                                    <a href="javascript:void(0);" class="text-muted">No contacts available.</a>
                                </li>
                            @endforelse
                        </ol>
                        {{-- <div class="mt-3 text-center">
                            <a href="javascript:void(0);" class="text-muted text-decoration-underline">View all Categories</a>
                        </div> --}}
                    </div>

                    <div class="card sidebar-alert bg-light border-0 text-center mx-4 mb-0 mt-3">
                        <div class="card-body">
                            <img src="{{ asset('build/images/logo-sm.png') }}" alt="">
                            <div class="mt-4">
                                <h5>API Docs</h5>
                                <p class="text-muted lh-base">Build your own solutions using our API.</p>
                                <a href="#" class="btn btn-primary btn-label rounded-pill"><i class="ri-code-fill label-icon align-middle rounded-pill fs-16 me-2"></i>
                                    View API Docs</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div> <!-- end card-->
        </div> <!-- end .rightbar-->

    </div> <!-- end col -->
</div>


@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- apexcharts -->
<script src="{{ asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('build/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('build/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('build/libs/swiper/swiper-bundle.min.js')}}"></script>
<!-- dashboard init -->
<script src="{{ asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Pass the data to the chart options
        var linechartcustomerColors = getChartColorsArray("sms_report_charts");
        if (linechartcustomerColors) {
            axios.get('/get-monthly-message-data')
            .then(function (response) {
                const data = response.data;
                let months = [];
                let blockedData = [];
                let totalSentData = [];
                let deliveredData = [];

                data.forEach(item => {
                    const monthYear = new Date(item.year, item.month - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
                    months.push(monthYear);
                    blockedData.push(item.blocked);
                    totalSentData.push(item.total_sent);
                    deliveredData.push(item.delivered);
                });
                
                var options = {
                    series: [
                        {
                            name: "Total Sent",
                            type: "bar",
                            data: totalSentData,
                        },
                        {
                            name: "Blocked",
                            type: "area",
                            data: blockedData,
                        },
                        {
                            name: "Delivered",
                            type: "line",
                            data: deliveredData,
                        },
                    ],
                    chart: {
                        height: 370,
                        type: "line",
                        toolbar: {
                            show: false,
                        },
                    },
                    stroke: {
                        curve: "straight",
                        dashArray: [0, 0, 8],
                        width: [2, 0, 2.2],
                    },
                    fill: {
                        opacity: [0.9, 0.3, 1],
                    },
                    markers: {
                        size: [0, 0, 0],
                        strokeWidth: 2,
                        hover: {
                            size: 4,
                        },
                    },
                    xaxis: {
                        categories: months,
                        axisTicks: {
                            show: false,
                        },
                        axisBorder: {
                            show: false,
                        },
                    },
                    grid: {
                        show: true,
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        padding: {
                            top: 0,
                            right: -2,
                            bottom: 15,
                            left: 10,
                        },
                    },
                    legend: {
                        show: true,
                        horizontalAlign: "center",
                        offsetX: 0,
                        offsetY: -5,
                        markers: {
                            width: 9,
                            height: 9,
                            radius: 6,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 0,
                        },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "30%",
                            barHeight: "70%",
                        },
                    },
                    colors: linechartcustomerColors,
                    tooltip: {
                        shared: true,
                        y: [
                            {
                                formatter: function (y) {
                                    if (typeof y !== "undefined") {
                                        return y.toFixed(0);
                                    }
                                    return y;
                                },
                            },
                            {
                                formatter: function (y) {
                                    if (typeof y !== "undefined") {
                                        return "" + y.toFixed(0) + "";
                                    }
                                    return y;
                                },
                            },
                            {
                                formatter: function (y) {
                                    if (typeof y !== "undefined") {
                                        return y.toFixed(0) + "";
                                    }
                                    return y;
                                },
                            },
                        ],
                    },
                };

                var chart = new ApexCharts(
                    document.querySelector("#sms_report_charts"),
                    options
                );
                chart.render();
            })
            .catch(function (error) {
                console.error('Error fetching monthly message data:', error);
            });
        }

        // Simple Donut Charts
        var chartDonutBasicColors = getChartColorsArray("network-count-pie");

        if (chartDonutBasicColors) {
            // Fetch the messages per network from the backend
            axios.get('/messages-per-network')
                .then(function (response) {
                    var data = response.data;

                    // Prepare the data for the donut chart
                    var labels = Object.keys(data);  // Get network names
                    var series = Object.values(data);  // Get message counts

                    // Build the ApexCharts donut chart options
                    var options = {
                        series: series,  // Data from the backend
                        labels: labels,  // Network names from the backend
                        chart: {
                            height: 333,
                            type: "donut",
                        },
                        legend: {
                            position: "bottom",
                        },
                        stroke: {
                            show: false,
                        },
                        dataLabels: {
                            dropShadow: {
                                enabled: false,
                            },
                        },
                        colors: chartDonutBasicColors,  // Colors
                    };

                    // Initialize the chart
                    var chart = new ApexCharts(
                        document.querySelector("#network-count-pie"),
                        options
                    );
                    chart.render();
                })
                .catch(function (error) {
                    console.error('Error fetching messages per network:', error);
                });
        }

        // get colors array from the string
        function getChartColorsArray(chartId) {
            if (document.getElementById(chartId) !== null) {
                var colors = document
                    .getElementById(chartId)
                    .getAttribute("data-colors");
                if (colors) {
                    colors = JSON.parse(colors);
                    return colors.map(function (value) {
                        var newValue = value.replace(" ", "");
                        if (newValue.indexOf(",") === -1) {
                            var color = getComputedStyle(
                                document.documentElement
                            ).getPropertyValue(newValue);
                            if (color) return color;
                            else return newValue;
                        } else {
                            var val = value.split(",");
                            if (val.length == 2) {
                                var rgbaColor = getComputedStyle(
                                    document.documentElement
                                ).getPropertyValue(val[0]);
                                rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                                return rgbaColor;
                            } else {
                                return newValue;
                            }
                        }
                    });
                } else {
                    console.warn("data-colors atributes not found on", chartId);
                }
            }
        }

            
    });
</script>
@endsection
