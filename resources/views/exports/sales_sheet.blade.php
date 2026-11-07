<table>
    <tr>
        <th colspan="4" style="font-weight: bold; text-align: left;">Prepared by {{ Auth::user()->name ?? 'System' }}</th>
    </tr>
    <tr>
        <th></th>
        <th colspan="3" style="font-weight: bold; text-align: left;">HCE SALES ANALYSIS</th>
    </tr>
    <tr>
        <th></th>
        <th colspan="3" style="font-weight: bold; text-align: left;">{{ $title }}</th>
    </tr>
    <tr>
        <th></th>
        <th colspan="3" style="font-weight: bold; text-align: left;">{{ strtoupper($date) }}</th>
    </tr>

    <tr>
        <th style="font-weight: bold; border: 1px solid #000;">NO</th>
        <th style="font-weight: bold; border: 1px solid #000;">CHURCH</th>
        <th style="font-weight: bold; border: 1px solid #000;">CONTACTS</th>
        <th style="font-weight: bold; border: 1px solid #000;">T. ISSUED</th>
        <th style="font-weight: bold; border: 1px solid #000;">T. RETURNED</th>
        
        @foreach($products as $pName => $price)
            <th style="font-weight: bold; border: 1px solid #000;">{{ strtoupper($pName) }}</th>
        @endforeach
        
        <th style="font-weight: bold; border: 1px solid #000;">DELIVERY</th>

        @foreach($products as $pName => $price)
            <th style="font-weight: bold; border: 1px solid #000;">{{ number_format($price, 2) }}</th>
        @endforeach

        <th style="font-weight: bold; border: 1px solid #000;">TOTAL</th>
        <th style="font-weight: bold; border: 1px solid #000;">PAID</th>
        <th style="font-weight: bold; border: 1px solid #000;">BALANCE</th>
    </tr>

    @foreach($rows as $index => $row)
    <tr>
        <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
        <td style="border: 1px solid #000;">{{ $row['church'] }}</td>
        <td style="border: 1px solid #000;">{{ $row['contacts'] }}</td>
        <td style="border: 1px solid #000;">{{ $row['issued'] ?: '-' }}</td>
        <td style="border: 1px solid #000;">{{ $row['returned'] ?: '-' }}</td>

        @foreach($products as $pName => $price)
            <td style="border: 1px solid #000;">{{ $row['itemQtys'][$pName] ?: '-' }}</td>
        @endforeach

        <td style="border: 1px solid #000;">{{ $row['delivery'] > 0 ? number_format($row['delivery'], 2) : '-' }}</td>

        @foreach($products as $pName => $price)
            <td style="border: 1px solid #000;">{{ $row['itemAmounts'][$pName] > 0 ? number_format($row['itemAmounts'][$pName], 2) : '-' }}</td>
        @endforeach

        <td style="border: 1px solid #000;">{{ number_format($row['total'], 2) }}</td>
        <td style="border: 1px solid #000;">{{ $row['paid'] > 0 ? number_format($row['paid'], 2) : '-' }}</td>
        <td style="border: 1px solid #000;">{{ number_format($row['balance'], 2) }}</td>
    </tr>
    @endforeach

    <tr>
        <td style="border: 1px solid #000;"></td>
        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">TOTALS</td>
        <td style="border: 1px solid #000; background-color: #F4B084;"></td>
        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ $totals['issued'] }}</td>
        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ $totals['returned'] }}</td>

        @foreach($products as $pName => $price)
            <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ $totals['items'][$pName]['qty'] }}</td>
        @endforeach

        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ number_format($totals['delivery'], 2) }}</td>

        @foreach($products as $pName => $price)
            <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ number_format($totals['items'][$pName]['amount'], 2) }}</td>
        @endforeach

        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ number_format($totals['total'], 2) }}</td>
        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ number_format($totals['paid'], 2) }}</td>
        <td style="font-weight: bold; border: 1px solid #000; background-color: #F4B084;">{{ number_format($totals['balance'], 2) }}</td>
    </tr>
    @if(isset($weeklySummary))
        <tr><td></td></tr>
        <tr><td></td></tr>

        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #92D050; border: 1px solid #000; text-align: left;">TOTAL CUPS</th>
            @foreach($productNames as $pName)
                <th style="font-weight: bold; background-color: #92D050; border: 1px solid #000;">{{ strtoupper($pName) }}</th>
            @endforeach
            <th></th> </tr>
        
        @foreach($weeklySummary as $weekLabel => $data)
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #92D050; border: 1px solid #000;">{{ $weekLabel }}</td>
            @foreach($productNames as $pName)
                <td style="border: 1px solid #000; background-color: #E2EFDA;">
                    {{ $data['products'][$pName] > 0 ? number_format($data['products'][$pName]) : '-' }}
                </td>
            @endforeach
        </tr>
        @endforeach
        
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #92D050; border: 1px solid #000;">TOTAL</td>
            @foreach($productNames as $pName)
                <td style="font-weight: bold; background-color: #92D050; border: 1px solid #000;">
                    {{ $productTotals[$pName] > 0 ? number_format($productTotals[$pName]) : '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold; background-color: #FFFF00; border: 1px solid #000;">{{ number_format($grandTotalCups) }}</td>
        </tr>

        <tr><td></td></tr>
        <tr><td></td></tr>

        <tr>
            <th style="font-weight: bold; background-color: #FFC000; border: 1px solid #000; text-align: left;">WEEK</th>
            <th style="font-weight: bold; background-color: #FFC000; border: 1px solid #000; text-align: left;">SALES</th>
        </tr>
        
        @foreach($weeklySummary as $weekLabel => $data)
        <tr>
            <td style="font-weight: bold; background-color: #FFC000; border: 1px solid #000;">{{ str_replace(' ', '', $weekLabel) }}</td>
            <td style="font-weight: bold; border: 1px solid #000; background-color: #FFF2CC;">
                {{ $data['sales'] > 0 ? number_format($data['sales'], 2) : '-' }}
            </td>
        </tr>
        @endforeach
        
        <tr>
            <td style="font-weight: bold; background-color: #FFC000; border: 1px solid #000;">TOTAL SALES</td>
            <td style="font-weight: bold; background-color: #FFFF00; border: 1px solid #000;">{{ number_format($grandTotalSales, 2) }}</td>
        </tr>
    @endif
</table>