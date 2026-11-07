<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

if (!function_exists('isStatus')) {
    function isStatus($val)
    {
        switch ($val) {
            case 1:
                return '<span class="badge bg-danger-subtle text-danger">Blocked</span>';
            case 0:
                return '<span class="badge bg-success-subtle text-success">Active</span>';
            case "Freelancer":
                return '<span class="badge bg-secondary-subtle text-secondary">' . $val . '</span>';
            default:
                return '<span class="badge bg-secondary-subtle text-secondary">Unknown</span>';
        }
    }
}

if (!function_exists('identifyOperator')) {
    function identifyOperator($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Format Number to 2547xx or 2541xx
        $formattedPhoneNumber = formatPhoneNumber($phoneNumber);

        // Ensure the phone number starts with 254
        if (strpos($phoneNumber, '254') !== 0) {
            return 'Unknown';
        }

        // Define the operator prefixes
        $operators = [
            'Safaricom' => ['25470', '25471', '25472', '25474', '254757', '254758', '254759', '254768', '254769', '25479', '254110', '254111', '254112', '254113', '254114', '254115'],
            'Airtel' => ['25473', '254747', '254748', '254750', '254751', '254752', '254753', '254754', '254755', '254756', '254785', '254786', '254787', '254788', '254789', '254100', '254101', '254102', '254103', '254104', '254105', '254106'],
            'Equitel' => ['254763', '254764', '254765', '254766'],
            'Telkom' => ['25470', '25471', '25472', '25473', '25474', '25475', '25476', '25477', '25478', '25479'],
        ];

        // Check the phone number against the prefixes
        foreach ($operators as $operator => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (strpos($formattedPhoneNumber, $prefix) === 0) {
                    return $operator;
                }
            }
        }

        // If no match is found
        return 'Unknown';
    }
}

if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($number)
    {
        if (is_null($number)) {
            return null;
        }

        // Remove any spaces or non-digit characters (except +)
        $number = preg_replace('/\s+/', '', $number);
        $number = preg_replace('/[^0-9+]/', '', $number);

        // Handle cases where the number starts with +254
        if (strpos($number, '+254') === 0) {
            return substr($number, 1);
        }

        // Handle cases where the number starts with 07xxx or 01xxx
        if (strpos($number, '07') === 0 || strpos($number, '01') === 0) {
            return '254' . substr($number, 1);
        }

        // Handle cases where the number starts with 7xxx or 1xxx
        if (strpos($number, '7') === 0 || strpos($number, '1') === 0) {
            return '254' . $number;
        }

        // If the number is already in the correct format, return it as is
        if (strpos($number, '254') === 0) {
            return $number;
        }

        // For any other cases, return the original number unmodified
        return $number;
    }
}

if (!function_exists('getInvalidPhoneNumber')) {
    function getInvalidPhoneNumber($number)
    {
        if (is_null($number)) {
            return null;
        }

        // Remove any spaces or non-digit characters (except +)
        $number = preg_replace('/\s+/', '', $number);
        $number = preg_replace('/[^0-9+]/', '', $number);

        // Handle cases where the number starts with +254
        if (strpos($number, '+254') === 0) {
            $formattedNumber = substr($number, 1);
        }
        // Handle cases where the number starts with 07xxx or 01xxx
        elseif (strpos($number, '07') === 0 || strpos($number, '01') === 0) {
            $formattedNumber = '254' . substr($number, 1);
        }
        // Handle cases where the number starts with 7xxx or 1xxx
        elseif (strpos($number, '7') === 0 || strpos($number, '1') === 0) {
            $formattedNumber = '254' . $number;
        }
        // If the number is already in the correct format
        elseif (strpos($number, '254') === 0) {
            $formattedNumber = $number;
        }
        // For any other cases, return the original number unmodified
        else {
            return 'invalid';
        }

        // Check if the formatted number is exactly 12 characters long
        if (strlen($formattedNumber) !== 12) {
            return 'invalid';
        }

        return $formattedNumber;
    }
}

function maskPhoneNumber($phone)
{
    return str_repeat('*', strlen($phone) - 2) . substr($phone, -2);
}

function maskEmail($email)
{
    $emailParts = explode("@", $email);
    $name = substr($emailParts[0], 0, 3); // Show only first 3 characters
    return $name . str_repeat('*', strlen($emailParts[0]) - 3) . '@' . $emailParts[1];
}

if (!function_exists('updateCounters')) {
    function updateCounters($message)
    {
        $messageLength = strlen($message);
        $optOutLength = 15;
        $maxMessageLength = 160;

        // Calculate total length including the opt-out string
        $totalLength = $messageLength + $optOutLength;

        // Calculate number of messages needed
        $messages = ceil($totalLength / $maxMessageLength);

        // Calculate remaining characters in the current message
        $remaining = ($messages * $maxMessageLength) - $totalLength;

        return [
            'length' => $messageLength,
            'remaining' => $remaining,
            'messages' => $messages
        ];
    }
}

if (!function_exists('updateVoiceCounters')) {
    function updateVoiceCounters($message)
    {
        $messageLength = strlen($message);
        $optOutLength = 0;
        $maxMessageLength = 160;

        // Calculate total length including the opt-out string
        $totalLength = $messageLength + $optOutLength;

        // Calculate number of messages needed
        $messages = ceil($totalLength / $maxMessageLength);

        // Calculate remaining characters in the current message
        $remaining = ($messages * $maxMessageLength) - $totalLength;

        return [
            'length' => $messageLength,
            'remaining' => $remaining,
            'messages' => $messages
        ];
    }
}

if (!function_exists('getInitials')) {
    /**
     * Get initials from a name.
     *
     * @param string $name
     * @return string
     */
    function getInitials($name)
    {
        // Handle empty or null names
        if (empty($name)) {
            return '?';
        }

        // Clean and split name by spaces, removing empties
        $words = array_values(array_filter(explode(' ', trim($name))));
        $initials = '';

        $count = count($words);

        if ($count >= 2) {
            // Get initials of first two words
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } elseif ($count === 1) {
            // Get first two letters of a single word (e.g., Edward → ED)
            $initials = strtoupper(substr($words[0], 0, 2));
        } else {
            $initials = '?';
        }

        return $initials;
    }
}

if (!function_exists('greetBasedOnTime')) {
    function greetBasedOnTime()
    {
        $currentHour = now()->format('H');

        if ($currentHour < 12) {
            return 'Good Morning';
        } elseif ($currentHour < 17) {
            return 'Good Afternoon';
        } elseif ($currentHour < 21) {
            return 'Good Evening';
        } else {
            return 'Good Night';
        }
    }
}

if (!function_exists('sendWAOtpMessage')) {
    // Function to send a message via WhatsApp API
    function sendWAOtpMessage($version, $phone, $otp, $lang)
    {
        // Your WhatsApp Cloud API Access Token
        $accessToken = env('WA_ACCESS_TOKEN');
        $phoneNumberId = env('WA_PHONE_NUMBER_ID');

        $url = "https://graph.facebook.com/$version/$phoneNumberId/messages";

        $data = [
            "messaging_product" => "whatsapp",
            "to" => $phone,
            "type" => "template",
            "template" => [
                "name" => "otp",
                "language" => [
                    "code" => $lang,
                    "policy" => "deterministic"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $options = [
            'http' => [
                'header' => "Authorization: Bearer $accessToken\r\nContent-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    }
}

if (!function_exists('sendZeptoMail')) {
    function sendZeptoMail($toEmail, $templateKey, array $mergeData, $subject = null, $from_name = null)
    {
        $url = 'https://api.zeptomail.com/v1.1/email/template';

        // Base payload
        $data = [
            "from" => [
                "address" => env('ZEPTOMAIL_FROM_ADDRESS'),
                "name"    => $from_name ?? env('ZEPTOMAIL_FROM_NAME'),
            ],
            "to" => [
                [
                    "email_address" => [
                        "address" => $toEmail,
                    ]
                ]
            ],
            "template_key" => $templateKey,
            "merge_info"   => $mergeData,
        ];

        // Conditionally add subject
        if (!empty($subject)) {
            $data['subject'] = $subject;
        }

        $client = new Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => env('ZEPTOMAIL_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $data,
                'timeout' => 10,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error('ZeptoMail Error', [
                'email' => $toEmail,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('sendZeptoMailBatch')) {
    /**
     * @param string $templateKey
     * @param array $recipients Format: [['email' => '...', 'name' => '...', 'merge_info' => [...]]]
     * @param string|null $fromName
     */
    function sendZeptoMailBatch($templateKey, array $recipients, $fromName = null)
    {
        $url = 'https://api.zeptomail.com/v1.1/email/template/batch';

        $toData = array_map(function ($recipient) {
            return [
                "email_address" => [
                    "address" => $recipient['email'],
                    "name"    => $recipient['name'] ?? ''
                ],
                "merge_info" => $recipient['merge_info']
            ];
        }, $recipients);

        $payload = [
            "template_key" => $templateKey,
            "from" => [
                "address" => env('ZEPTOMAIL_FROM_ADDRESS'),
                "name"    => $fromName ?? env('ZEPTOMAIL_FROM_NAME'),
            ],
            "to" => $toData
        ];

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => env('ZEPTOMAIL_API_KEY'),
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30, // Increased timeout for batches
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error('ZeptoMail Batch Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        // Initialize suffix and formatted amount
        $suffix = '';

        if ($amount >= 1000000) {
            $formattedAmount = $amount / 1000000;
            $suffix = 'M';
        } elseif ($amount >= 1000) {
            $formattedAmount = $amount / 1000;
            $suffix = 'K';
        } else {
            $formattedAmount = $amount; // Keep it in original form for amounts less than 1000
        }

        return [
            'amount' => round($formattedAmount, 2), // Format to two decimal places
            'suffix' => $suffix
        ];
    }

    function successResponse($data = [], $message = 'Success', $statusCode = 200)
    {
        // If it's paginated data (instance of LengthAwarePaginator or Paginator)
        if ($data instanceof \Illuminate\Contracts\Pagination\Paginator) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data->items(),
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ]
            ], $statusCode);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }


    function errorResponse($message = 'Error', $errors = [], $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }
}

// app/Helpers/MergeTagHelper.php

if (!function_exists('applyMergeTags')) {
    function applyMergeTags(string $content, $branch = null, $contact = null)
    {
        $tags = [
            'account' => [
                '#branch_name#'   => 'branch_name',
                '#COMPANY_EMAIL#'  => 'company_email',
                '#COMPANY_PHONE#'  => 'company_phone',
                '#BALANCE#'        => 'balance',
                '#kra_pin_number#' => 'kra_pin_number',
                '#LAST_SEEN#'      => 'last_seen',
            ],
            'contact' => [
                '#FIRST_NAME#' => 'first_name',
                '#LAST_NAME#'  => 'last_name',
                '#PHONE#'      => 'phone',
            ],
        ];

        if ($branch) {
            foreach ($tags['account'] as $tag => $column) {
                $content = str_replace(
                    $tag,
                    data_get($branch, $column, ''),
                    $content
                );
            }
        }

        if ($contact) {
            foreach ($tags['contact'] as $tag => $column) {
                $content = str_replace(
                    $tag,
                    data_get($contact, $column, ''),
                    $content
                );
            }
        }

        return $content;
    }
}
