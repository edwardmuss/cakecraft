<?php

namespace App\DTOs;

class PaymentData
{
    public function __construct(
        public string  $accountNumber,
        public string  $reference,
        public float   $amount,
        public string  $method,
        public ?string $senderPhone = null,
        public ?string $senderName  = null,
        public array   $rawPayload  = []
    ) {}
}
