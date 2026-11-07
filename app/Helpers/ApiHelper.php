<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Billing;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Otp;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\Billing\AccountLedgerService;
use App\Services\MsisdnDecoderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiHelper
{
    public static function getUserAndAccount()
    {
        try {
            $payload   = JWTAuth::parseToken()->getPayload();
            $userId    = $payload->get('user_id');
            $branchId = $payload->get('branch_id');
        } catch (JWTException $e) {
            throw new \Exception('Invalid or expired token');
        }

        $user = User::find($userId);
        if (!$user) throw new \Exception('User not found');

        $branch = Account::find($branchId);
        if (!$branch) throw new \Exception('Account not found');

        $auth = self::validateAuthorization($user);
        if (!$auth->status) throw new \Exception($auth->message);

        return [$user, $branch];
    }

    public static function normalizeMessages(array $inputMessages)
    {
        $inputMessages = isset($inputMessages[0]) ? $inputMessages : [$inputMessages];
        $messages = [];

        foreach ($inputMessages as $msg) {
            if (isset($msg['phone']) && strpos($msg['phone'], ',') !== false) {
                foreach (explode(',', $msg['phone']) as $phone) {
                    $messages[] = array_merge($msg, ['phone' => trim($phone)]);
                }
            } else {
                $messages[] = $msg;
            }
        }

        return $messages;
    }

    /**
     * MAIN CHANGE: This now works with balance_minor (KES cents),
     * while keeping sms_units (segments) for API response compatibility.
     *
     * @param array   $messageData
     * @param int     $userId
     * @param Account $branch
     * @param string  $batchId
     * @param int     $remainingMinor  (by ref) current wallet in cents
     * @param int     $totalDeductedMinor (by ref) deducted cents in this batch
     */
    public static function processMessage($messageData, $userId, $branch, $batchId, &$remainingMinor, &$totalDeductedMinor)
    {
        $isHashed = !empty($messageData['hashed']) && $messageData['hashed'] === true;

        if ($isHashed) {
            $decoder = new MsisdnDecoderService();
            $messageData['phone'] = $decoder->getMsisdnFromData($messageData, 'phone') ?? $messageData['phone'];
        }

        $uniqueId = (string) Str::uuid();
        $formattedContact = isset($messageData['phone']) ? formatPhoneNumber($messageData['phone']) : null;
        $contactStatus    = isset($messageData['phone']) ? getInvalidPhoneNumber($messageData['phone']) : null;

        // validate
        $validationError = self::validateMessage($messageData, $branch, $formattedContact, $uniqueId);
        if ($validationError) {
            return [false, $validationError];
        }

        $timeToSend = isset($messageData['time_to_send'])
            ? Carbon::createFromFormat('Y-m-d H:i', $messageData['time_to_send'])
            : Carbon::now();

        $counter  = updateCounters($messageData['message']);
        $length   = (int) $counter['length'];
        $smsUnits = (int) $counter['messages']; // segments
        $operator = identifyOperator($formattedContact);

        // cost in minor
        $unitPriceMinor = (int) round(((float) $branch->sms_pricing) * 100);
        $costMinor      = $smsUnits * $unitPriceMinor;

        // decide status
        if ($contactStatus === 'invalid') {
            $deliveryStatus = 'invalid_number';
            $remarks = 'Invalid number';
            $costMinor = 0;
            $smsUnits = 0;
        } elseif (self::isBlockedContact($formattedContact, $branch)) {
            $deliveryStatus = 'contact_blocked';
            $remarks = 'Contact blocked';
            $costMinor = 0;
            $smsUnits = 0;
        } elseif ($remainingMinor < $costMinor) {
            $deliveryStatus = 'insufficient_balance';
            $remarks = 'Insufficient balance';
            $costMinor = 0;
            $smsUnits = 0;
        } else {
            $deliveryStatus = 'queued';
            $remarks = 'Message queued successfully';
            $remainingMinor -= $costMinor;
            $totalDeductedMinor += $costMinor;
        }

        // save message (keep units column as sms segments for reporting)
        Message::create([
            'sender'            => $messageData['sender'],
            'phone'             => $formattedContact,
            'message'           => $messageData['message'],
            'correlator'        => $messageData['correlator'] ?? null,
            'delivery_endpoint' => $messageData['delivery_endpoint'] ?? null,
            'branch_id'        => $branch->id,
            'created_by'        => $userId,
            'units'             => $smsUnits,
            'source'            => 'API',
            'length'            => $length,
            'operator'          => $operator,
            'batch_id'          => $batchId,
            'time_to_send'      => $timeToSend,
            'api_unique_id'     => $uniqueId,
            'sms_status'        => $deliveryStatus,
        ]);

        $result = self::buildResponse(
            $messageData,
            $formattedContact,
            $operator,
            $batchId,
            $uniqueId,
            $timeToSend,
            $smsUnits,
            $length,
            $remarks,
            $deliveryStatus,
            $costMinor
        );

        // optional extra info (won’t break existing clients)
        // $result['data']['cost_kes'] = round($costMinor / 100, 2);

        return [true, $result];
    }

    public static function validateMessage($messageData, $branch, $formattedContact, $uniqueId)
    {
        $requiredFields = ['sender', 'phone', 'message'];
        $missing = [];

        foreach ($requiredFields as $field) {
            if (empty($messageData[$field])) $missing[] = $field;
        }

        if (!empty($missing)) {
            return [
                'status' => false,
                'message' => 'Required fields are missing: ' . implode(', ', $missing),
                'data' => [
                    'short_code' => $messageData['sender'] ?? 'N/A',
                    'phone' => $formattedContact,
                    'uniqueId' => $uniqueId,
                    'sms_units' => 0,
                    'remarks' => 'Missing fields: ' . implode(', ', $missing),
                ]
            ];
        }

        if (!$branch->shortcodes()->where('name', $messageData['sender'])->exists()) {
            return [
                'status' => false,
                'message' => "Unauthorized sender '{$messageData['sender']}' for this account.",
                'data' => [
                    'short_code' => $messageData['sender'],
                    'phone' => $formattedContact,
                    'uniqueId' => $uniqueId,
                    'sms_units' => 0,
                    'remarks' => 'Unauthorized sender'
                ]
            ];
        }

        if (isset($messageData['time_to_send']) && !Carbon::hasFormat($messageData['time_to_send'], 'Y-m-d H:i')) {
            return [
                'status' => false,
                'message' => "Invalid date format for 'time_to_send'. Expected format: Y-m-d H:i.",
                'data' => [
                    'short_code' => $messageData['sender'],
                    'phone' => $formattedContact,
                    'uniqueId' => $uniqueId,
                    'sms_units' => 0,
                    'remarks' => 'Invalid time format'
                ]
            ];
        }

        return null;
    }

    public static function isBlockedContact($phone, $branch)
    {
        $contact = Contact::where('phone', $phone)
            ->where('branch_id', $branch->id)
            ->first();

        return $contact && $contact->blocked;
    }

    public static function buildResponse($messageData, $phone, $operator, $batchId, $uniqueId, $timeToSend, $units, $length, $remarks, $deliveryStatus, $costMinor = null)
    {
        $isOtp = !empty($messageData['is_otp']) && $messageData['is_otp'] === true;

        return [
            'status' => $deliveryStatus === 'queued',
            'message' => $remarks,
            'data' => [
                'short_code'        => $messageData['sender'],
                'phone'             => $phone,
                'operator'          => $operator,
                'message'           => $isOtp ? null : $messageData['message'],
                'correlator'        => $messageData['correlator'] ?? null,
                'delivery_endpoint' => $messageData['delivery_endpoint'] ?? null,
                'batch_id'          => $batchId,
                'uniqueId'          => $uniqueId,
                'time_to_send'      => $timeToSend,
                'sms_units'         => $units,
                'length'            => $length,
                'remarks'           => $remarks,
                'cost_kes'          => $costMinor !== null ? round($costMinor / 100, 2) : 0,
                'created_at'        => now()->toDateTimeString(),
            ]
        ];
    }

    /**
     * MAIN CHANGE: updateBilling now logs MINOR wallet and updates balance_minor.
     * We keep billing.initial_units/new_units for now (storing cents).
     */
    public static function updateBilling($branch, $userId, $remainingMinor, $totalDeductedMinor, $batchId)
    {
        $initialMinor = (int) $branch->balance_minor;
        $pricePerSmsKes = (float) $branch->sms_pricing;

        // $branch->balance_minor = (int) $remainingMinor;
        // $branch->save();

        $totalSegments = round(($totalDeductedMinor / $pricePerSmsKes) / 100, 2); // because your units = number of messages/segments

        app(AccountLedgerService::class)->apply(
            accountId: $branch->id,
            createdBy: $userId,
            amountMinor: -$totalDeductedMinor, // deduction
            currency: $branch->currency ?? 'KES',
            source: 'sms',
            eventType: 'charge',
            idempotencyKey: "sms:batch:{$branch->id}:{$batchId}",
            reference: $batchId,
            description: 'Outgoing SMS queued - Batch ID: ' . $batchId .
                ' | Cost: KES ' . round($totalDeductedMinor / 100, 2),
            meta: [
                'batch_id' => $batchId,
                'segments' => $totalSegments,
                'sms_pricing_kes' => $pricePerSmsKes,
                'opening_minor' => $initialMinor,
                'closing_minor' => $remainingMinor,
                'total_cost_kes' => round($totalDeductedMinor / 100, 2),
            ]
        );

        // Billing::create([
        //     'branch_id'    => $branch->id,
        //     'created_by'    => $userId,
        //     'initial_units' => $initialMinor,          // TEMP stores cents
        //     'new_units'     => (int) $remainingMinor,  // TEMP stores cents
        //     'billing_type'  => 'API Outgoing Queue SMS',
        //     'remarks'       => "Batch ID: {$batchId} | Deducted: " . round($totalDeductedMinor / 100, 2) . " KES",
        // ]);

        ActivityLogger::log(
            'New API Messages Queued',
            "API Messages queued successfully in batch: {$batchId}",
            null,
            null,
            $branch->id
        );
    }

    public static function validateAuthorization($user): object
    {
        if (!$user->isActive)
            return (object)['status' => false, 'message' => "You have been suspended from Using the System!"];

        if (!$user->branch->isActive)
            return (object)['status' => false, 'message' => "The Account associated with you has been suspended!"];

        return (object)['status' => true];
    }

    public static function decodeResponse($response)
    {
        if ($response[0]['status'])
            return [
                'status' => true,
                'message' => 'Message successfully queued!',
                'data' => [
                    'correlator' => $response[0]['data']['correlator'],
                    'uniqueId' => $response[0]['data']['uniqueId'],
                    'phone' => $response[0]['data']['phone'],
                    'sms_units' => $response[1][3]
                ]
            ];

        return $response[0];
    }

    //////////////////////////////////
    /// OTP Message (unchanged)
    //////////////////////////////////

    public static function generateOtp(int $length = 6, string $type = 'numeric'): string
    {
        switch ($type) {
            case 'alpha':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'mixed':
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'numeric':
            default:
                $characters = '0123456789';
        }

        return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
    }

    public static function getOrCreateOtp($branch_id, $phone, $expiryTime = 300, $length = 6, $characterType = 'numeric')
    {
        $now = Carbon::now();

        $otp = Otp::where('branch_id', $branch_id)
            ->where('phone', $phone)
            ->where('used', false)
            ->latest()
            ->first();

        if ($otp) {
            $lifeSpan = $otp->expiry_time;
            $expiryAt = $otp->expires_at;

            $timePassed = $otp->created_at->diffInSeconds($now);
            $percentagePassed = $timePassed / $lifeSpan;

            if ($percentagePassed < 0.7 && $now->lessThan($expiryAt)) {
                return $otp;
            }
        }

        $newOtp = self::generateOtp($length, $characterType);

        return Otp::create([
            'branch_id'      => $branch_id,
            'phone'          => $phone,
            'otp'            => $newOtp,
            'expiry_time'    => $expiryTime,
            'length'         => $length,
            'character_type' => $characterType,
            'expires_at'     => $now->addSeconds($expiryTime),
        ]);
    }
}
