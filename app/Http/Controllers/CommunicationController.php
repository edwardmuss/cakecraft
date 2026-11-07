<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Billing;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunicationController extends Controller
{
    public function index()
    {
        return view('communication.index');
    }

    /**
     * Preview merged content (AJAX)
     */
    public function preview(Request $request)
    {
        $branch = Branch::where('id', Auth::user()->branch_id)->first();

        return response()->json([
            'preview' => applyMergeTags($request->message, $branch)
        ]);
    }

    public function send(Request $request)
    {
        // Conditional Validation:
        // - Subject is required ONLY IF channel is email
        // - Time to send is required ONLY IF channel is sms
        $request->validate([
            'channel'      => 'required|in:sms,email',
            'message'      => 'required|string',
            'status'       => 'required|in:active,inactive,dormant,all',
            'subject'      => 'required_if:channel,email|nullable|string|max:255',
            'time_to_send' => 'required_if:channel,sms|nullable|date',
        ]);

        $query = Branch::query();

        // Apply Status Filters
        if ($request->status === 'active') {
            $query->where('isActive', true)->where('locked', false);
        } elseif ($request->status === 'inactive') {
            $query->where(function ($q) {
                $q->where('isActive', false)
                    ->orWhere('locked', true);
            });
        } elseif ($request->status === 'dormant') {
            $query->where('last_seen', '<', now()->subDays(60));
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No accounts found matching the selected criteria.'
            ], 422);
        }

        $batchId = Str::uuid();
        $emailRecipients = [];

        foreach ($accounts as $branch) {

            $personalizedMessage = applyMergeTags($request->message, $branch);
            $personalizedSubject = applyMergeTags($request->subject ?? '', $branch);

            /** EMAIL CHANNEL **/
            /** EMAIL PREPARATION **/
            if ($request->channel === 'email') {
                $emailRecipients[] = [
                    'email' => $branch->branch_email,
                    'name'  => $branch->branch_name,
                    'merge_info' => [
                        'branch_name'   => $branch->branch_name,
                        'kra_pin_number' => $branch->kra_pin_number,
                        'balance'        => number_format($branch->balance, 2),
                        'message'        => $personalizedMessage,
                        'subject'        => $personalizedSubject,
                        'product_name'   => "RebueText (RT)",
                        'login_url_link' => "[https://rebuetext.com](https://rebuetext.com)",
                        'team'           => "RebueText Team",
                        'date'           => now()->format('d M Y h:i a'),
                    ]
                ];
                continue;
            }

            /** SMS CHANNEL **/
            $counter = updateCounters($personalizedMessage);
            $formattedContact = formatPhoneNumber($branch->company_phone);
            $contact = getInvalidPhoneNumber($branch->company_phone);
            $operator = identifyOperator($contact);

            Message::create([
                'sender'       => 'CLOUD_REBUE',
                'phone'        => $formattedContact,
                'message'      => $personalizedMessage,
                'time_to_send' => $request->time_to_send,
                'branch_id'   => Auth::user()->branch_id,
                'created_by'   => Auth::id(),
                'length'       => $counter['length'],
                'units'        => $counter['messages'],
                'operator'     => $operator,
                'source'       => 'Web-Admin',
                'batch_id'     => $batchId,
                'sms_status'   => 'queued',
            ]);
        }

        /** TRIGGER BATCH EMAIL WITH 500 LIMIT HANDLING **/
        if ($request->channel === 'email' && !empty($emailRecipients)) {
            // Wrap in a collection and chunk by 500 (ZeptoMail API limit)
            collect($emailRecipients)->chunk(500)->each(function ($chunk) use ($request) {
                sendZeptoMailBatch(
                    '2d6f.10b4e7b927f81eea.k1.f3b054a0-e23c-11f0-abf8-525400d4bb1c.19b59fb16ea',
                    $chunk->toArray(),
                    "RebueText Updates"
                );
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin communications (' . $request->channel . ') queued successfully for ' . $accounts->count() . ' recipients.'
        ]);
    }

    public function previewCount(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,dormant,all',
            'message' => 'required|string',
        ]);

        $query = Branch::query();

        if ($request->status === 'active') {
            $query->where('isActive', true)->where('locked', false);
        }

        if ($request->status === 'inactive') {
            $query->where(
                fn($q) =>
                $q->where('isActive', false)->orWhere('locked', true)
            );
        }

        if ($request->status === 'dormant') {
            $query->where('last_seen', '<', now()->subDays(60));
        }

        $accounts = $query->get();

        $totalRecipients = $accounts->count();
        $totalUnits = 0;

        foreach ($accounts as $branch) {
            $message = applyMergeTags($request->message, $branch);
            $counter = updateCounters($message);
            $totalUnits += $counter['messages'];
        }

        return response()->json([
            'recipients' => $totalRecipients,
            'estimated_units' => $totalUnits,
        ]);
    }
}
