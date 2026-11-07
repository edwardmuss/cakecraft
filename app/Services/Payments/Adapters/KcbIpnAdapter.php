<?php

namespace App\Services\Payments\Adapters;

use App\DTOs\PaymentData;

class KcbIpnAdapter
{
    public function fromPayload(array $data): PaymentData
    {
        $customerRef = $data['customerReference'] ?? '';
        [, $accountNumber] = array_pad(explode('-', $customerRef, 2), 2, null);

        return new PaymentData(
            accountNumber: $accountNumber ?? '',
            reference: $data['requestId'] ?? $data['transactionReference'],
            amount: (float) ($data['transactionAmount'] ?? 0),
            method: 'kcb_mpesa',
            senderPhone: $data['customerMobileNumber'] ?? null,
            senderName: $data['customerName'] ?? null,
            rawPayload: $data
        );
    }
}
