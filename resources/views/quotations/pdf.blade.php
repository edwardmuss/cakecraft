<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quote_number }}</title>
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
        /* Custom stamps for quotation statuses */
        .stamp-accepted { color: #28a745; border-color: #28a745; }
        .stamp-declined { color: #dc3545; border-color: #dc3545; } 
        .stamp-sent { color: #007bff; border-color: #007bff; }
        .stamp-draft { color: #6c757d; border-color: #6c757d; }
        .stamp-invoiced { color: #17a2b8; border-color: #17a2b8; }
        
        .footer-notes {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 13px;
            color: #555;
            page-break-inside: avoid;
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
                        <img src="{{ \App\Support\Brand::logoFile() }}" alt="{{ $branch->branch_name }}">
                    </div>
                    <br>
                    <span style="font-size: 14px; font-weight: normal;">
                        PIN: {{ $branch->kra_pin_number }}<br>
                        Phone: {{ $branch->company_phone }}
                    </span>
                </td>
                <td class="invoice-details">
                    <h2>QUOTATION</h2>
                    <strong>Quote #:</strong> {{ $quotation->quote_number }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation->quote_date)->format('d M, Y') }}<br>
                    
                    @if($quotation->expiry_date)
                        <strong>Valid Until:</strong> {{ \Carbon\Carbon::parse($quotation->expiry_date)->format('d M, Y') }}<br>
                    @endif
                    
                    @if($quotation->subject)
                        <strong>Subject:</strong> {{ $quotation->subject }}<br>
                    @endif
                    
                    <div class="status-stamp stamp-{{ $quotation->status }}">
                        {{ str_replace('_', ' ', $quotation->status) }}
                    </div>
                </td>
            </tr>
        </table>

        <table style="margin-bottom: 30px;">
            <tr>
                <td>
                    <strong>Prepared For:</strong><br>
                    {{ $quotation->customer->name }}<br>
                    @if($quotation->customer->organization_name)
                        {{ $quotation->customer->organization_name }}<br>
                    @endif
                    {{ $quotation->customer->phone }}<br>
                    {{ $quotation->customer->email ?? '' }}<br>
                    {{ $quotation->customer->billing_address ?? '' }}
                </td>
                @if($quotation->customer->delivery_location)
                <td style="text-align: right;">
                    <strong>Delivery Location:</strong><br>
                    {{ $quotation->customer->delivery_location }}
                </td>
                @endif
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item / Description</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->item_name }}
                        @if($item->size_label || $item->flavour_name)
                            <br><small style="color:#777;">{{ collect([$item->size_label, $item->flavour_name])->filter()->implode(' · ') }}</small>
                        @endif
                        @if($item->cake_message)
                            <br><small style="color:#777;">Message: {{ $item->cake_message }}</small>
                        @endif</td>
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
                <td class="text-right">{{ number_format($quotation->subtotal, 2) }} KES</td>
            </tr>
            <tr>
                <td>Tax ({{ $branch->vat_percentage ?? 0 }}%):</td>
                <td class="text-right">{{ number_format($quotation->tax_amount, 2) }} KES</td>
            </tr>
            
            @if($quotation->shipping_fee > 0)
            <tr>
                <td>Shipping Fee:</td>
                <td class="text-right">{{ number_format($quotation->shipping_fee, 2) }} KES</td>
            </tr>
            @endif

            <tr>
                <td class="bold" style="border-top: 1px solid #ddd;">Grand Total:</td>
                <td class="bold text-right" style="border-top: 1px solid #ddd; color: #dc3545;">
                    {{ number_format($quotation->total_amount, 2) }} KES
                </td>
            </tr>
        </table>
        
        <div style="clear: both;"></div>

        <div class="footer-notes">
            @if($quotation->terms_and_conditions)
                <div style="margin-bottom: 20px;">
                    <strong>Terms & Conditions:</strong><br>
                    <span style="margin-top: 5px; display: inline-block;">
                        {!! nl2br(e($quotation->terms_and_conditions)) !!}
                    </span>
                </div>
            @endif
            
            @if($quotation->notes)
                <div style="margin-bottom: 20px;">
                    <strong>Notes:</strong><br>
                    <span style="margin-top: 5px; display: inline-block;">
                        {!! nl2br(e($quotation->notes)) !!}
                    </span>
                </div>
            @endif
        </div>

    </div>
</body>
</html>