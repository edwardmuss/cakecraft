@extends('layouts.master')
@section('title') Financial Dashboard @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Dashboards @endslot
    @slot('title') Financial Overview @endslot
@endcomponent

<!-- Filter Bar & Display Period -->
<div class="row mb-3 pb-1">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1">Financial Health</h4>
                <p class="text-muted mb-0">Track your expected revenue vs actual cash flow and expenses.</p>
                <!-- NEW: Clear period indicator -->
                <div class="mt-2 text-primary fw-medium">
                    <i class="ri-calendar-event-line align-middle me-1"></i> Showing Data for: {{ $displayPeriod }}
                </div>
            </div>
            <div class="mt-3 mt-lg-0">
                <form action="{{ route('finance.dashboard') }}" method="GET" class="d-flex gap-2">
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}" title="Select a specific month">
                    <button type="submit" class="btn btn-primary"><i class="ri-filter-2-line align-bottom me-1"></i> Filter</button>
                    @if(request('month'))
                        <a href="{{ route('finance.dashboard') }}" class="btn btn-soft-secondary">Clear</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- KPI Widgets -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Expected Income (Billed)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">KES {{ number_format($expectedIncome, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="ri-file-list-3-line text-primary"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Actual Income (Collected)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">KES {{ number_format($actualIncome, 2) }}</h4>
                        @php $collectionRate = $expectedIncome > 0 ? ($actualIncome / $expectedIncome) * 100 : 0; @endphp
                        <span class="badge {{ $collectionRate >= 90 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                            {{ number_format($collectionRate, 1) }}% Collection Rate
                        </span>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="ri-wallet-3-line text-success"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Expenses</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">KES {{ number_format($actualExpense, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                            <i class="ri-shopping-cart-2-line text-danger"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Net Profit</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            KES {{ number_format($netProfit, 2) }}
                        </h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title {{ $netProfit >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' }} rounded fs-3">
                            <i class="ri-funds-line {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 12-Month Trend Chart -->
    <div class="col-xl-8">
        <div class="card card-height-100">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Revenue vs. Expenses (Trailing 12 Months)</h4>
            </div>
            <div class="card-body p-0 pb-2">
                <div class="w-100">
                    <div id="financial_trend_chart" data-colors='["#3577f1", "#0ab39c", "#f06548"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Breakdown Donut -->
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Top 5 Expenses</h4>
            </div>
            <div class="card-body">
                @if(count($pieData) > 0)
                    <div id="expense_breakdown_chart" data-colors='["#3577f1", "#0ab39c", "#f06548", "#f7b84b", "#878a99"]' class="apex-charts" dir="ltr"></div>
                @else
                    <div class="text-center mt-5 text-muted">
                        <i class="ri-pie-chart-2-line fs-1" style="opacity: 0.5;"></i>
                        <p class="mt-3">No expenses recorded for this period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- NEW: Deep Dive Tables -->
<div class="row">
    <!-- Categories Table -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">All Expenses by Category</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th>Category Name</th>
                                <th class="text-end">Total Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allExpenseCategories as $cat)
                                <tr>
                                    <td><span class="fw-medium">{{ $cat->name }}</span></td>
                                    <td class="text-end fw-semibold text-danger">{{ number_format($cat->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center p-3 text-muted">No expenses recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Top Suppliers (Vendors)</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th>Supplier Name</th>
                                <th class="text-end">Total Spend (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplierTableData as $sup)
                                <tr>
                                    <td><span class="fw-medium">{{ $sup->name }}</span></td>
                                    <td class="text-end fw-semibold">{{ number_format($sup->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center p-3 text-muted">No vendor expenses recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    // Safe parsing for PHP arrays into JS numbers
    const chartExpectedData = {!! json_encode($chartExpected) !!}.map(Number);
    const chartActualData = {!! json_encode($chartActual) !!}.map(Number);
    const chartExpenseData = {!! json_encode($chartExpense) !!}.map(Number);

    // --- 1. Line/Bar Chart (12 Months Trend) ---
    var trendColors = getChartColorsArray("financial_trend_chart");
    var trendOptions = {
        series: [{
            name: 'Expected Income',
            type: 'column',
            data: chartExpectedData
        }, {
            name: 'Actual Income',
            type: 'area',
            data: chartActualData
        }, {
            name: 'Expenses',
            type: 'line',
            data: chartExpenseData
        }],
        chart: {
            height: 370,
            type: 'line',
            toolbar: { show: false }
        },
        stroke: {
            curve: 'smooth',
            dashArray: [0, 0, 8],
            width: [0, 2, 2]
        },
        fill: {
            opacity: [1, 0.1, 1]
        },
        markers: {
            size: [0, 4, 4],
            strokeWidth: 2,
            hover: { size: 4 }
        },
        xaxis: {
            categories: {!! json_encode($chartMonths) !!},
            labels: {
                rotate: -45, // NEW: Rotated labels
                rotateAlways: true,
                style: { fontSize: '12px' }
            },
            axisTicks: { show: false },
            axisBorder: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return value.toLocaleString('en-US');
                }
            }
        },
        colors: trendColors,
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return "KES " + y.toLocaleString('en-US', {minimumFractionDigits: 2});
                    }
                    return y;
                }
            }
        }
    };
    var trendChart = new ApexCharts(document.querySelector("#financial_trend_chart"), trendOptions);
    trendChart.render();

    // --- 2. Donut Chart (Expense Breakdown) ---
    @if(count($pieData) > 0)
    var pieColors = getChartColorsArray("expense_breakdown_chart");
    
    // Explicitly parse pie data as numbers to fix the empty chart
    const safePieData = {!! json_encode($pieData) !!}.map(Number);

    var pieOptions = {
        series: safePieData,
        labels: {!! json_encode($pieLabels) !!},
        chart: {
            type: 'donut',
            height: 333
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            show: true,
            position: 'bottom'
        },
        colors: pieColors,
        tooltip: {
            y: {
                formatter: function (val) {
                    return "KES " + val.toLocaleString('en-US', {minimumFractionDigits: 2});
                }
            }
        }
    };
    var pieChart = new ApexCharts(document.querySelector("#expense_breakdown_chart"), pieOptions);
    pieChart.render();
    @endif

    // Utility function
    function getChartColorsArray(chartId) {
        if (document.getElementById(chartId) !== null) {
            var colors = document.getElementById(chartId).getAttribute("data-colors");
            if (colors) {
                colors = JSON.parse(colors);
                return colors.map(function (value) {
                    var newValue = value.replace(" ", "");
                    if (newValue.indexOf(",") === -1) {
                        var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                        if (color) return color;
                        else return newValue;
                    }
                });
            }
        }
        return ["#3577f1", "#0ab39c", "#f06548"];
    }
</script>
@endsection