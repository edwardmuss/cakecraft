<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $payment->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; font-size: 14px; }
        .receipt-box { max-width: 600px; margin: auto; padding: 30px; border: 1px solid #eee; text-align: center;}
        .amount { font-size: 30px; font-weight: bold; margin: 20px 0; color: #28a745; }
        .details-table { width: 100%; margin-top: 20px; text-align: left; border-collapse: collapse; }
        .details-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .details-table .label { font-weight: bold; width: 40%; color: #555; }
        #logo img {
            max-width: 50mm;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>PAYMENT RECEIPT</h2>
        <div id="logo">
            <img src="{{ \App\Support\Brand::logoFile() }}" alt="{{ $payment->invoice->branch->branch_name }}">
        </div>
        <br>
        <h4>{{ $payment->branch->branch_name }}</h4>
        <p>Thank you for your payment!</p>
        
        <div class="amount">
            KES {{ number_format($payment->amount, 2) }}
        </div>

        <table class="details-table">
            <tr><td class="label">Date:</td><td>{{ $payment->created_at->format('d M, Y h:i A') }}</td></tr>
            <tr><td class="label">Customer:</td><td>{{ $payment->invoice->customer->organization_name ?? $payment->invoice->customer->name }}</td></tr>
            <tr><td class="label">Invoice Applied To:</td><td>{{ $payment->invoice->invoice_number }}</td></tr>
            <tr><td class="label">Payment Method:</td><td>{{ $payment->method }}</td></tr>
            <tr><td class="label">Transaction Ref:</td><td>{{ $payment->reference }}</td></tr>
        </table>
    </div>
</body>
</html>