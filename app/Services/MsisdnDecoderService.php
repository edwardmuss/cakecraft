<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MsisdnDecoderService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('MSISDN_DECODER_API_KEY');
        $this->baseUrl = env('MSISDN_DECODER_BASE_URL');;
    }

    /**
     * Decode a hash to get the MSISDN
     *
     * @param string $hash
     * @return string Returns the decoded MSISDN or the original hash if decoding fails
     */
    public function decodeHash(string $hash): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/decode-hash/freemium/{$hash}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['msisdn']) && !empty($data['msisdn'])) {
                    return $data['msisdn'];
                }

                // If msisdn is not found but hash is returned, log and return hash
                Log::warning('MSISDN decoder API returned success but no msisdn field', [
                    'hash' => $hash,
                    'response' => $data
                ]);

                return $hash;
            }

            // Log API error but don't throw exception
            Log::warning('MSISDN decoder API request failed', [
                'hash' => $hash,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $hash;
        } catch (\Exception $e) {
            Log::error('MSISDN decoder API exception', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);

            return $hash;
        }
    }

    /**
     * Helper method to get MSISDN from data array with fallback
     *
     * @param array $data
     * @param string $hashKey
     * @param string $msisdnKey
     * @return string
     */
    public function getMsisdnFromData(array $data, string $hashKey = 'MSISDN', string $msisdnKey = 'msisdn'): string
    {
        // First check if we already have a plain MSISDN
        if (isset($data[$msisdnKey]) && !empty($data[$msisdnKey])) {
            return $data[$msisdnKey];
        }

        // Check if we have a hash to decode
        if (isset($data[$hashKey]) && !empty($data[$hashKey])) {
            return $this->decodeHash($data[$hashKey]);
        }

        // Return empty string if neither is found
        return '';
    }
}
