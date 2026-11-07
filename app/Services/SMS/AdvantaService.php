<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class AdvantaService extends BaseSmsService
{
    protected string $route = 'advanta';
    protected int $limit = 20; // Advanta limit per request

    public function send($messages)
    {
        $client = new Client();
        $body = $this->constructBody($messages);

        try {
            $response = $client->post(env('ADVANTA_SMS_ENDPOINT'), [
                RequestOptions::HEADERS => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                RequestOptions::JSON => $body
            ]);

            $data = json_decode((string)$response->getBody(), true);
            Log::channel($this->logChannel)->info("Response: " . json_encode($data));

            foreach ($data['responses'] as $index => $responseItem) {
                $message = $messages[$index];

                if ($responseItem['response-code'] == "200") {
                    $message->update([
                        'processed' => true,
                        'sms_status' => 'sent',
                        'unique_id' => $responseItem['messageid']
                    ]);
                } else {
                    $message->update([
                        'processed' => 2,
                        'unique_id' => $responseItem['messageid'] ?? null,
                        'sms_status' => 'Failed',
                        'remarks' => $responseItem['response-description']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('api_errors')->error($e->getMessage());
            Log::channel('api_errors')->error($e->getTraceAsString());

            foreach ($messages as $message) {
                $message->update([
                    'processed' => 3,
                    'sms_status' => 'Failed',
                    'remarks' => $e->getMessage()
                ]);
            }
        }
    }

    protected function constructBody($messages): array
    {
        $list = [];
        foreach ($messages as $msg) {
            $list[] = [
                'partnerID'   => env('ADVANTA_PARTNER_ID'),
                'apikey'      => env('ADVANTA_API_KEY'),
                'mobile'      => $msg->phone,
                'message'     => $msg->message,
                'shortcode'   => $msg->sender,
                'clientsmsid' => $msg->id,
                'pass_type'   => 'plain',
            ];
        }

        $payload = [
            'count'   => count($list),
            'smslist' => $list
        ];

        Log::channel($this->logChannel)->info("Request Body: " . json_encode($payload));

        return $payload;
    }
}
