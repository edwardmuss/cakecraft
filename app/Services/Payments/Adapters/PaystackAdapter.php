<?php

namespace App\Services\Payments\Adapters;

use App\DTOs\PaymentData;
use App\Models\STKPush;

class PaystackAdapter
{
    public function fromCallback(array $paymentDetails, STKPush $stk): PaymentData
    {
        return new PaymentData(
            accountNumber: $stk->account,
            reference: $paymentDetails['data']['receipt_number'],
            amount: (float) $stk->amount,
            method: $paymentDetails['data']['channel'],
            rawPayload: $paymentDetails
        );
    }
}
