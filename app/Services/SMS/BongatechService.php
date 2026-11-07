<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class BongatechService extends BaseSmsService
{
    protected string $route = 'bongatech';
    protected int $limit = 100; // Bongatech allows 100 per request

    public function send($messages)
    {
        $client = new Client();
        $body = $this->constructBody($messages);

        try {
            $response = $client->post(env('BONGATECH_SMS_ENDPOINT'), [
                RequestOptions::HEADERS => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('BONGATECH_TOKEN'),
                ],
                RequestOptions::JSON => $body,
                RequestOptions::TIMEOUT => 300,
            ]);

            $data = json_decode((string) $response->getBody(), true);
            Log::channel($this->logChannel)->info("Bongatech Response: " . json_encode($data));

            // Response can be an array of results or a single object
            if (isset($data[0])) {
                $this->handleResponseArray($data, $messages);
            } elseif (isset($data['status']) && $data['status'] === false) {
                // Handle top-level failure like "Unauthenticated"
                $this->handleGlobalFailure($data, $messages);
            }
        } catch (\Exception $e) {
            Log::channel('api_errors')->error("Bongatech Exception: " . $e->getMessage());
            Log::channel('api_errors')->error($e->getTraceAsString());

            $messages->each->update([
                'processed' => 3,
                'remarks'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build Bongatech request body
     */
    protected function constructBody($messages): array
    {
        $payload = $messages->map(function ($msg) {
            return [
                'sender'     => $msg->sender,
                'message'    => $msg->message,
                'phone'      => $msg->phone,
                'correlator' => $msg->id, // must match data.correlator in response
                'endpoint' => route('delivery-receipt'),
            ];
        })->values()->toArray();

        Log::channel($this->logChannel)->info("Bongatech Request Body: " . json_encode($payload));
        return $payload;
    }

    /**
     * Handle Bongatech response array
     */
    protected function handleResponseArray(array $responses, $messages): void
    {
        $messagesById = $messages->keyBy('id');

        foreach ($responses as $res) {
            $correlator = $res['data']['correlator'] ?? null;
            $status = $res['status'] ?? false;
            $msgText = $res['message'] ?? 'Unknown response';

            if (!$correlator || !isset($messagesById[$correlator])) {
                Log::warning("Bongatech correlator {$correlator} not found in message batch.");
                continue;
            }

            $message = $messagesById[$correlator];
            $uniqueId = $res['data']['uniqueId'] ?? null;

            if ($status === true) {
                // Successfully queued
                $message->update([
                    'processed'  => true,
                    'sms_status' => 'sent',
                    'unique_id'  => $uniqueId,
                ]);
            } else {
                // Failed to queue or invalid number
                $remarks = $res['data']['remarks'] ?? $msgText;
                $message->update([
                    'processed'  => 2,
                    'sms_status' => $remarks,
                    'unique_id'  => $uniqueId,
                    'remarks'    => $remarks,
                ]);

                Log::error("Bongatech failed for message ID {$correlator}: {$remarks}");
            }
        }
    }

    /**
     * Handle a global-level error (e.g., Unauthenticated)
     */
    protected function handleGlobalFailure(array $data, $messages): void
    {
        $errorMsg = $data['message'] ?? 'Global Bongatech API Error';
        Log::error("Bongatech Global Error: " . $errorMsg);

        $messages->each->update([
            'processed' => 3,
            'sms_status' => $errorMsg,
            'remarks'   => $errorMsg,
        ]);
    }
}
