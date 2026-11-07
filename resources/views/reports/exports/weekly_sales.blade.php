{{-- TABLE 1: TOTAL CUPS (GREEN) --}}
<table>
    <thead>
        <tr>
            <th colspan="{{ count($productNames) + 1 }}" style="background-color: #70ad47; color: #000000; font-weight: bold; text-align: center;">TOTAL CUPS</th>
        </tr>
        <tr>
            <th style="background-color: #70ad47; color: #000000; font-weight: bold;"></th>
            @foreach($productNames as $pName)
                <th style="background-color: #70ad47; color: #000000; font-weight: bold;">{{ strtoupper($pName) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $productTotals = array_fill_keys($productNames, 0); @endphp
        
        @foreach($weeks as $week)
            <tr>
                <td style="background-color: #70ad47; color: #000000; font-weight: bold;">{{ strtoupper($week) }}</td>
                @foreach($productNames as $pName)
                    @php
                        $qty = $cupsData[$week][$pName];
                        $productTotals[$pName] += $qty;
                    @endphp
                    <td style="background-color: #c6e0b4; color: #000000;">{{ $qty }}</td>
                @endforeach
            </tr>
        @endforeach
        
        {{-- TOTALS ROW --}}
        <tr>
            <td style="background-color: #70ad47; color: #000000; font-weight: bold;">TOTAL</td>
            @foreach($productNames as $pName)
                <td style="background-color: #70ad47; color: #000000; font-weight: bold;">{{ $productTotals[$pName] }}</td>
            @endforeach
        </tr>
    </tbody>
</table>

{{-- SPACER ROW --}}
<table><tr><td></td></tr></table>

{{-- TABLE 2: SALES (YELLOW) --}}
<table>
    <thead>
        <tr>
            <th style="background-color: #ffc000; color: #000000; font-weight: bold;">WEEK</th>
            <th style="background-color: #ffc000; color: #000000; font-weight: bold;">SALES</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotalSales = 0; @endphp
        @foreach($weeks as $week)
            @php $grandTotalSales += $salesData[$week]; @endphp
            <tr>
                <td style="background-color: #ffc000; color: #000000; font-weight: bold;">{{ strtoupper(str_replace(' ', '', $week)) }}</td>
                <td style="background-color: #ffc000; color: #000000;">{{ $salesData[$week] }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="background-color: #ffc000; color: #000000; font-weight: bold;">TOTAL SALES</td>
            <td style="background-color: #ffc000; color: #000000; font-weight: bold;">{{ $grandTotalSales }}</td>
        </tr>
    </tbody>
</table>