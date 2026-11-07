<tbody>
    @foreach ($payments as $payment)
        <tr>
            <td>{{ $payment->reference }}</td>
            <td>{{ $payment->amount }}</td>
            <td>{{ $payment->units }}</td>
            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y h:i A') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('payments.showInvoice', $payment->id) }}" class="btn btn-sm btn-soft-info"><i class="ri-eye-line me-2"></i>View Invoice</a>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
