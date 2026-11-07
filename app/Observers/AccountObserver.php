<?php

namespace App\Observers;

use App\Models\Account;
use App\Services\SMSService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $branch): void
    {
        // 1. Prepare the notification message
        $message = "New Account Alert!\n";
        $message .= "Company: {$branch->branch_name}\n";
        $message .= "Email: {$branch->company_email}\n";
        $message .= "Phone: {$branch->company_phone}\n";
        $message .= "Acc No: {$branch->kra_pin_number}";

        // 2. Send SMS to Admin
        try {
            // Resolving the SMSService from the service container
            $smsService = app(SMSService::class);

            // Format to international format for safety
            $adminPhone = config('services.system.admin_phone');

            $smsService->sendSMS($adminPhone, $message);
        } catch (\Exception $e) {
            Log::error('Admin SMS notification failed: ' . $e->getMessage());
        }

        // 3. Send Email to Admin
        try {
            // Using Mail::raw is the quickest way to send a simple text email 
            // without needing to build a new ZeptoMail template.
            Mail::raw($message, function ($mail) {
                $mail->to(config('services.system.admin_email'))
                    ->subject('New Account Registration - RebueText');
            });

            /* * Alternatively, if you want to use ZeptoMail:
             * * sendZeptoMail('info@rebuetext.com', 'YOUR_ADMIN_TEMPLATE_ID', [
             * 'branch_name' => $branch->branch_name,
             * 'company_email' => $branch->company_email,
             * 'company_phone' => $branch->company_phone,
             * ]);
             */
        } catch (\Exception $e) {
            Log::error('Admin Email notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Account "updated" event.
     */
    public function updated(Account $branch): void
    {
        //
    }

    /**
     * Handle the Account "deleted" event.
     */
    public function deleted(Account $branch): void
    {
        //
    }
}
