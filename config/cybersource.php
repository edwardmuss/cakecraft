<?php

return [
    'merchant_id' => env('CYBERSOURCE_MERCHANT_ID'),
    'api_key_id' => env('CYBERSOURCE_API_KEY_ID'),
    'secret_key' => env('CYBERSOURCE_SECRET_KEY'),
    'environment' => env('CYBERSOURCE_ENVIRONMENT', 'SANDBOX'), // Use PRODUCTION in live mode
];
