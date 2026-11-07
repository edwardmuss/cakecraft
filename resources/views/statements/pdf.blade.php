<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Statement - {{ $statement['customer']->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 13px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 5px; vertical-align: top; }
        .header-table td { padding-bottom: 20px; }
        .header-table .title { font-size: 24px; font-weight: bold; color: #333; }
        .invoice-details { text-align: right; }
        .items-table { border: 1px solid #ddd; margin-top: 20px; }
        .items-table th { background: #f8f9fa; border-bottom: 2px solid #ddd; padding: 10px 5px; font-weight: bold; font-size: 12px; }
        .items-table td { border-bottom: 1px solid #eee; padding: 8px 5px; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .bold { font-weight: bold; }
        .bg-light { background-color: #f8f9fa; }
        
        #logo img { max-width: 70mm; height: auto; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td class="title">
                    <div id="logo">
                        <img src="{{ \App\Support\Brand::logoFile() }}" alt="{{ $branch->branch_name }}">
                    </div>
                    <br>
                    <span style="font-size: 13px; font-weight: normal;">
                        PIN: {{ $branch->kra_pin_number }}<br>
                        Phone: {{ $branch->company_phone }}
                    </span>
                </td>
                <td class="invoice-details">
                    <h2 style="margin-bottom: 5px;">STATEMENT OF ACCOUNT</h2>
                    <strong>Date Generated:</strong> {{ now()->format('d M, Y') }}<br>
                    <strong>Statement Period:</strong><br>
                    {{ $statement['start_date']->format('d M, Y') }} to {{ $statement['end_date']->format('d M, Y') }}
                </td>
            </tr>
        </table>

        <table style="margin-bottom: 20px;">
            <tr>
                <td>
                    <strong>Customer Details:</strong><br>
                    {{ $statement['customer']->name }}<br>
                    @if($statement['customer']->organization_name)
                        {{ $statement['customer']->organization_name }}<br>
                    @endif
                    {{ $statement['customer']->phone }}<br>
                    {{ $statement['customer']->email ?? '' }}
                </td>
                <td class="text-right">
                    <div style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd; display: inline-block;">
                        <span style="font-size: 12px; color: #666;">Total Amount Due</span><br>
                        <strong style="font-size: 18px;">KES {{ number_format($statement['closing_balance'], 2) }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Details</th>
                    <th class="text-right">Charges</th>
                    <th class="text-right">Payments</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance -->
                <tr class="bg-light">
                    <td colspan="5" class="text-right bold">Opening Balance as of {{ $statement['start_date']->format('d M Y') }}</td>
                    <td class="text-right bold">{{ number_format($statement['opening_balance'], 2) }}</td>
                </tr>

                <!-- Transactions -->
                @forelse($statement['transactions'] as $tx)
                <tr>
                    <td>{{ $tx['date']->format('d M Y') }}</td>
                    <td>{{ $tx['reference'] }}</td>
                    <td>
                        <strong>{{ $tx['type'] }}</strong><br>
                        <span style="font-size: 11px; color: #666;">{{ $tx['description'] }}</span>
                    </td>
                    <td class="text-right">{{ $tx['debit'] > 0 ? number_format($tx['debit'], 2) : '' }}</td>
                    <td class="text-right">{{ $tx['credit'] > 0 ? number_format($tx['credit'], 2) : '' }}</td>
                    <td class="text-right">{{ number_format($tx['balance'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">No transactions found in this period.</td>
                </tr>
                @endforelse

                <!-- Closing Balance -->
                <tr class="bg-light">
                    <td colspan="3" class="text-right bold">Totals & Closing Balance</td>
                    <td class="text-right bold">{{ number_format($statement['total_debit'], 2) }}</td>
                    <td class="text-right bold">{{ number_format($statement['total_credit'], 2) }}</td>
                    <td class="text-right bold" style="font-size: 15px;">{{ number_format($statement['closing_balance'], 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #777;">
            This is a computer-generated statement. Please contact us if you notice any discrepancies.
        </div>
    </div>
</body>
</html>