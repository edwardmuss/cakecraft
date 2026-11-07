<?php

namespace App\Services\Payments\Adapters;

use App\DTOs\PaymentData;

class MpesaC2BAdapter
{
    public function fromRequest(array $data): PaymentData
    {
        return new PaymentData(
            accountNumber: $data['BillRefNumber'],
            reference: $data['TransID'],
            amount: (float) $data['TransAmount'],
            method: $data['TransactionType'] ?? 'mpesa',
            senderPhone: $data['MSISDN'] ?? null,
            senderName: $data['FirstName'] ?? null,
            rawPayload: $data
        );
    }
}
