<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SMSService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('SMS_ENDPOINT') . '/send-sms';
        $this->apiKey = env('SMS_API_KEY');
    }

    public function sendSMS($phone, $message, $sender = null)
    {
        $sender = $sender ?? env('SMS_SENDER');
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->apiUrl, [
            'sender' => $sender,
            'phone' => $phone,
            'message' => $message,
        ]);

        return $response->json();
    }
}
