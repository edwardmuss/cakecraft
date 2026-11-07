<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Models\NotificationLog;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendPaymentReceivedSMS implements ShouldQueue
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $customer = $invoice->customer;
        $branch = $invoice->branch;

        if (!$customer->phone) {
            return;
        }

        $balance = $invoice->total_amount - $invoice->amount_paid;

        // 1. Fetch custom template
        $template = \App\Models\SmsTemplate::where('branch_id', $branch->id)
            ->where('trigger_name', 'payment_received')
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
        // NEW: Generate Secure Receipt Link
        $receiptLink = URL::temporarySignedRoute(
            'payments.public_receipt',
            now()->addDays(7),
            ['id' => $payment->id]
        );

        // Format Order Details
        $orderDetails = $invoice->items->map(function ($item) {
            return $item->quantity . 'Pcs ' . $item->item_name;
        })->implode(', ');

        // 3. Set the raw message
        $rawMessage = $template && $template->message
            ? $template->message
            : "Hello {CustomerName}, we have received your payment of KES {PaymentAmount} for Invoice #{InvoiceNumber}. Download your receipt here: {ReceiptLink}";

        // 4. Swap placeholders for real data!
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
                '{PaymentLink}',
                '{PaymentAmount}',
                '{Balance}',
                '{ReceiptLink}'
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
                $paymentLink,
                number_format($payment->amount, 2),
                number_format($balance, 2),
                $receiptLink
            ],
            $rawMessage
        );

        // Optional: Only append this if they are using the default message
        if (!$template || empty($template->message)) {
            if ($balance > 0) {
                $message .= " Remaining balance: KES " . number_format($balance, 2) . ".";
            } else {
                $message .= " Your invoice is now fully paid. Thank you!";
            }
        }

        try {
            $response = $this->smsService->sendSMS($customer->phone, $message);
            $isSuccessful = isset($response['status']) && $response['status'] == 'success';

            NotificationLog::create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'trigger_type' => 'payment_received',
                'message' => $message,
                'is_successful' => $isSuccessful,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Payment Received SMS for Payment #{$payment->id}: " . $e->getMessage());
        }
    }
}
