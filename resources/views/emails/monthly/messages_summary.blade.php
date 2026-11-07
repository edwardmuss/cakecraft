@php
    $totalCount = $messagesByStatus->sum(fn($group) => count($group));

    $labels = [];
    $data = [];
    foreach ($messagesByStatus as $status => $group) {
        $labels[] = ucfirst(str_replace('_', ' ', $status));
        $data[] = count($group);
    }

    $colors = [
        '#EB6D2F', // Primary
        '#5A3733', // Secondary
        '#fdecf6', // Secondary Light
        '#f3eef6', // Primary Light
        '#a19da1', // Dark 60
    ];

    $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
        'type' => 'bar',
        'data' => [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Messages by Status',
                'data' => $data,
                'backgroundColor' => array_slice($colors, 0, count($labels)),
            ]]
        ],
        'options' => [
            'title' => [
                'display' => true,
                'text' => 'Monthly SMS Summary',
                'fontSize' => 18,
                'fontColor' => '#EB6D2F',
            ],
            'legend' => ['display' => false],
            'scales' => [
                'yAxes' => [[
                    'ticks' => ['beginAtZero' => true]
                ]]
            ]
        ]
    ]));
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f3eef6;
            padding: 20px;
        }

        h2 {
            color: #EB6D2F;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            background-color: #ffffff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #fdecf6;
            color: #5A3733;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #EB6D2F;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }

        .chart-title {
            font-weight: bold;
            color: #5A3733;
        }

        .summary-banner {
            background-color: #EB6D2F;
            color: white;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            margin-top: 30px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h2>SMS Summary – {{ now()->format('F Y') }}</h2>

    <p>Dear {{ $branch->branch_name }},</p>

    @if ($totalCount === 0)
        <p>
            We noticed you haven't sent any SMS messages this month ({{ now()->format('F Y') }}).
            If you need assistance using our messaging system, feel free to reach out or log into your dashboard for help.
        </p>

        <p>
            <a href="{{ url('/login') }}" class="button">Go to Dashboard</a>
        </p>
    @else
        <p>Below is your monthly SMS summary grouped by delivery status.</p>

        <p><strong>Total Messages:</strong> {{ $totalCount }}</p>

        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($messagesByStatus as $status => $group)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                        <td>{{ count($group) }}</td>
                        <td>{{ number_format((count($group) / $totalCount) * 100, 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="chart-title">Monthly Performance Chart:</p>
        <img src="{{ $chartUrl }}" alt="SMS Summary Chart" width="100%" style="max-width: 500px; margin: 20px 0;" />

        <p>
            <a href="{{ url('/login') }}" class="button">Go to Dashboard</a>
        </p>
    @endif

    <div class="footer">
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>

    <div class="summary-banner">
        Monthly SMS Performance – {{ now()->format('F Y') }}
    </div>
</body>
</html>
