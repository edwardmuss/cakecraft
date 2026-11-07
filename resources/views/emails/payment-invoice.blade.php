<p>Dear {{ $payment->branch_name }},</p>
<p>Thank you for your payment. Attached is your invoice for reference.</p>
<p>Invoice Number: #RT-{{ $payment->reference }}</p>
<p>Date: {{ now()->format('d-m-Y') }}</p>
<p>Amount: KES {{ number_format($payment->amount, 2) }}</p>
<p>Thank you for your business!</p>
