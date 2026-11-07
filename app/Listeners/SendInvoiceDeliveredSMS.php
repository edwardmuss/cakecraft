<?php

namespace App\Listeners;

use App\Events\InvoiceDelivered;
use App\Models\NotificationLog;
use App\Models\SmsTemplate;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendInvoiceDeliveredSMS implements ShouldQueue
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle(InvoiceDelivered $event): void
    {
        $invoice = $event->invoice;
        $customer = $invoice->customer;
        $branch = $invoice->branch;

        if (!$customer->phone) {
            return;
        }

        // Prevent duplicate SMS
        $alreadySent = NotificationLog::where('invoice_id', $invoice->id)
            ->where('trigger_type', 'delivered')
            ->where('is_successful', true)
            ->exists();

        if ($alreadySent) {
            return;
        }

        // 1. Fetch custom template
        $template = SmsTemplate::where('branch_id', $branch->id)
            ->where('trigger_name', 'delivered')
            ->first();

        // 2. Bail if they explicitly disabled this notification
        if ($template && !$template->is_active) {
            return;
        }

        // Generate Secure Links (valid for 7 days)
        $invoiceLink = URL::temporarySignedRoute(
            'invoices.public_pdf',
            now()->addDays(7),
            ['id' => $invoice->id]
        );
        $paymentLink = URL::temporarySignedRoute(
            'invoices.public_pay',
            now()->addDays(7),
            ['id' => $invoice->id]
        );

        // Format Order Details (e.g., "1 x Black Forest (2 kg, Vanilla)")
        $orderDetails = $invoice->items->map(function ($item) {
            $detail = collect([$item->size_label, $item->flavour_name])->filter()->implode(', ');
            return $item->quantity . ' x ' . $item->item_name . ($detail ? " ({$detail})" : '');
        })->implode(', ');

        $balance = $invoice->total_amount - $invoice->amount_paid;

        // 3. Set the raw message (Fallback if no custom template exists)
        $rawMessage = $template && $template->message
            ? $template->message
            : "Hello {CustomerName}, your order #{InvoiceNumber} has been successfully delivered. Thank you for choosing {BranchName}!";

        // 4. Standardized Replacement Array
        $message = str_replace(
            [
                '{CustomerName}',
                '{Organization}',
                '{InvoiceNumber}',
                '{TotalAmount}',
                '{Balance}',
                '{OrderDetails}',
                '{BranchName}',
                '{InvoiceLink}',
                '{PaymentLink}'
            ],
            [
                $customer->name,
                $customer->organization_name ?? $branch->branch_name,
                $invoice->invoice_number,
                number_format($invoice->total_amount, 2),
                number_format($balance, 2),
                $orderDetails,
                $branch->branch_name,
                $invoiceLink,
                $paymentLink
            ],
            $rawMessage
        );

        try {
            $response = $this->smsService->sendSMS($customer->phone, $message);
            $isSuccessful = isset($response['status']) && $response['status'] == 'success';

            NotificationLog::create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'trigger_type' => 'delivered',
                'message' => $message,
                'is_successful' => $isSuccessful,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Delivery SMS for Invoice #{$invoice->invoice_number}: " . $e->getMessage());
        }
    }
}
