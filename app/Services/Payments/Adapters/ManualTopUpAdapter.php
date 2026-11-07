<?php

namespace App\Services\Payments\Adapters;

use App\DTOs\PaymentData;
use Illuminate\Support\Facades\Auth;

class ManualTopUpAdapter
{
    public function fromAdmin(array $data): PaymentData
    {
        return new PaymentData(
            accountNumber: $data['kra_pin_number'],
            reference: $data['reference'] ?? 'ADMIN-' . now()->timestamp,
            amount: (float) $data['amount'],
            method: 'manual',
            senderPhone: null,
            senderName: Auth::user()->name ?? 'Admin',
            rawPayload: [
                'source' => 'admin_manual',
                'admin_id' => Auth::id(),
                'notes' => $data['notes'] ?? null
            ]
        );
    }
}
