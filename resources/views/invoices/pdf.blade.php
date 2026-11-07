<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 5px; vertical-align: top; }
        .header-table td { padding-bottom: 20px; }
        .header-table .title { font-size: 28px; font-weight: bold; color: #333; }
        .invoice-details { text-align: right; }
        .items-table th { background: #f8f9fa; border-bottom: 2px solid #ddd; padding: 10px 5px; font-weight: bold; }
        .items-table td { border-bottom: 1px solid #eee; padding: 10px 5px; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .totals-table { margin-top: 20px; border-top: 2px solid #ddd; width: 50%; float: right; }
        .totals-table td { padding: 8px 5px; }
        .totals-table .bold { font-weight: bold; }

        #logo img {
            max-width: 70mm;
            height: auto;
        }
        
        .status-stamp {
            display: inline-block;
            padding: 8px 15px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            border: 3px solid;
            border-radius: 5px;
            transform: rotate(-10deg);
            opacity: 0.7;
            margin-top: 10px;
        }
        .stamp-paid { color: #28a745; border-color: #28a745; }
        .stamp-pending, .stamp-processing { color: #dc3545; border-color: #dc3545; } 
        .stamp-partially_paid { color: #fd7e14; border-color: #fd7e14; }
        
        /* NEW: Footer styling for Notes & Payment Info */
        .footer-notes {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 13px;
            color: #555;
            page-break-inside: avoid; /* Prevents the footer from awkwardly splitting across pages */
        }
        .footer-notes strong {
            color: #333;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td class="title">
                    <div id="logo">
                        <img src="{{ \App\Support\Brand::logoFile() }}" alt="{{ $invoice->branch->branch_name }}">
                    </div>
                    <br>
                    <span style="font-size: 14px; font-weight: normal;">
                        PIN: {{ $invoice->branch->kra_pin_number }}<br>
                        Phone: {{ $invoice->branch->company_phone }}
                    </span>
                </td>
                <td class="invoice-details">
                    <h2>INVOICE</h2>
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Created:</strong> {{ $invoice->created_at->format('d M, Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') : 'N/A' }}<br>
                    @if($invoice->occasion_date)
                        <strong>Cake needed:</strong> {{ \Carbon\Carbon::parse($invoice->occasion_date)->format('l, d M Y') }}<br>
                    @endif
                    @if($invoice->recipient_name)
                        <strong>For:</strong> {{ $invoice->recipient_name }}{{ $invoice->occasion_type ? ' (' . ucfirst(str_replace('_', ' ', $invoice->occasion_type)) . ')' : '' }}<br>
                    @endif
                    <strong>Fulfilment:</strong> {{ ($invoice->fulfilment_type ?? 'pickup') === 'delivery' ? 'Delivery' : 'Pickup' }}<br>
                    
                    <div class="status-stamp stamp-{{ $invoice->status }}">
                        {{ in_array($invoice->status, ['pending', 'processing']) ? 'UNPAID' : str_replace('_', ' ', $invoice->status) }}
                    </div>
                </td>
            </tr>
        </table>

        <table style="margin-bottom: 30px;">
            <tr>
                <td>
                    <strong>Bill To:</strong><br>
                    {{ $invoice->customer->name }}<br>
                    @if($invoice->customer->organization_name)
                        {{ $invoice->customer->organization_name }}<br>
                    @endif
                    {{ $invoice->customer->phone }}<br>
                    {{ $invoice->customer->email ?? '' }}<br>
                    {{ $invoice->customer->billing_address ?? '' }}
                </td>
                @if($invoice->customer->delivery_location)
                <td style="text-align: right;">
                    <strong>Deliver To:</strong><br>
                    {{ $invoice->customer->delivery_location }}
                </td>
                @endif
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->item_name }}
                        @if($item->size_label || $item->flavour_name)
                            <br><small style="color:#777;">{{ collect([$item->size_label, $item->flavour_name])->filter()->implode(' · ') }}</small>
                        @endif
                        @if($item->cake_message)
                            <br><small style="color:#777;">Message: {{ $item->cake_message }}</small>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }} KES</td>
            </tr>
            <tr>
                <td>Tax ({{ $invoice->branch->vat_percentage ?? 0 }}%):</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }} KES</td>
            </tr>
            
            {{-- ONLY SHOW SHIPPING FEE IF IT IS GREATER THAN ZERO --}}
            @if($invoice->shipping_fee > 0)
            <tr>
                <td>Shipping Fee:</td>
                <td class="text-right">{{ number_format($invoice->shipping_fee, 2) }} KES</td>
            </tr>
            @endif

            <tr>
                <td class="bold">Grand Total:</td>
                <td class="bold text-right">{{ number_format($invoice->total_amount, 2) }} KES</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td class="text-right">{{ number_format($invoice->amount_paid, 2) }} KES</td>
            </tr>
            <tr>
                <td class="bold" style="border-top: 1px solid #ddd;">Balance Due:</td>
                <td class="bold text-right" style="border-top: 1px solid #ddd; color: #dc3545;">
                    {{ number_format($invoice->total_amount - $invoice->amount_paid, 2) }} KES
                </td>
            </tr>
        </table>
        
        <div style="clear: both;"></div>

        <div class="footer-notes">
            @if($invoice->notes)
                <div style="margin-bottom: 20px;">
                    <strong>Notes / Terms:</strong><br>
                    <span style="margin-top: 5px; display: inline-block;">
                        {!! nl2br(e($invoice->notes)) !!}
                    </span>
                </div>
            @endif

            @if($invoice->branch->payment_details)
                <div class="payment-instructions">
                    <strong>Payment Information:</strong><br>
                    <span style="margin-top: 5px; display: inline-block;">
                        @php
                            // Merge Tags logic for Payment Details
                            $rawDetails = $invoice->branch->payment_details;
                            
                            $tags = [
                                '{BranchName}' => $invoice->branch->branch_name,
                                '{BranchPhone}' => $invoice->branch->company_phone,
                                '{InvoiceNumber}' => $invoice->invoice_number,
                                '{TotalAmount}' => number_format($invoice->total_amount, 2),
                                '{Balance}' => number_format($invoice->total_amount - $invoice->amount_paid, 2)
                            ];

                            $processedDetails = str_replace(array_keys($tags), array_values($tags), $rawDetails);
                        @endphp
                        
                        {!! nl2br(e($processedDetails)) !!}
                    </span>
                </div>
            @endif
        </div>

    </div>
</body>
</html>