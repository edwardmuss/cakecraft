<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CyberSourcePaymentService
{
    protected $client;
    protected $merchantId;
    protected $apiKeyId;
    protected $secretKey;
    protected $environment;

    public function __construct()
    {
        $this->merchantId = config('cybersource.merchant_id');
        $this->apiKeyId = config('cybersource.api_key_id');
        $this->secretKey = config('cybersource.secret_key');
        $this->environment = config('cybersource.environment') === 'PRODUCTION'
            ? 'https://api.cybersource.com'
            : 'https://apitest.cybersource.com';

        $this->client = new Client([
            'base_uri' => $this->environment,
            'headers' => [
                'Content-Type' => 'application/json',
                'v-c-merchant-id' => $this->merchantId,
                'v-c-client-id' => $this->apiKeyId,
            ],
        ]);
    }

    public function processPayment($amount, $currency, $cardDetails, $billingDetails)
    {
        $payload = [
            'clientReferenceInformation' => [
                'code' => 'test_payment',
            ],
            'processingInformation' => [
                'commerceIndicator' => 'internet',
            ],
            'paymentInformation' => [
                'card' => [
                    'number' => $cardDetails['number'],
                    'expirationMonth' => $cardDetails['expirationMonth'],
                    'expirationYear' => $cardDetails['expirationYear'],
                    'securityCode' => $cardDetails['securityCode'],
                ],
            ],
            'orderInformation' => [
                'amountDetails' => [
                    'totalAmount' => $amount,
                    'currency' => $currency,
                ],
                'billTo' => [
                    'firstName' => $billingDetails['firstName'],
                    'lastName' => $billingDetails['lastName'],
                    'address1' => $billingDetails['address1'],
                    'locality' => $billingDetails['locality'],
                    'administrativeArea' => $billingDetails['administrativeArea'],
                    'postalCode' => $billingDetails['postalCode'],
                    'country' => $billingDetails['country'],
                    'email' => $billingDetails['email'],
                ],
            ],
        ];

        try {
            $response = $this->client->post('/pts/v2/payments', [
                'auth' => [$this->apiKeyId, $this->secretKey],
                'json' => $payload,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('CyberSource Payment Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
