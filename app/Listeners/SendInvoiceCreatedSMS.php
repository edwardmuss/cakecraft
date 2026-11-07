<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Models\NotificationLog;
use App\Models\SmsTemplate;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendInvoiceCreatedSMS implements ShouldQueue
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;
        $customer = $invoice->customer;
        $branch = $invoice->branch;

        $alreadySent = NotificationLog::where('invoice_id', $invoice->id)
            ->where('trigger_type', 'order_created')
            ->where('is_successful', true)
            ->exists();

        if ($alreadySent || ! $customer->phone) {
            return;
        }

        // Fetch the custom template
        $template = SmsTemplate::where('branch_id', $branch->id)
            ->where('trigger_name', 'order_created')
            ->where('is_active', true)
            ->first();

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

        // Fallback Template
        $rawMessage = $template && $template->message
            ? $template->message
            : "Hello {CustomerName},\nYour cake order #{InvoiceNumber} from {BranchName} is ready for payment.\nAmount: KES {TotalAmount}\nItems: {OrderDetails}\n\nClick here to pay: {PaymentLink}";

        // Standardized Replacement Array
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
            // Use $this->smsService
            $response = $this->smsService->sendSMS($customer->phone, $message);
            $isSuccessful = isset($response['status']) && $response['status'] == 'success';

            NotificationLog::create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'trigger_type' => 'order_created',
                'message' => $message,
                'is_successful' => $isSuccessful,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Order Created SMS for Invoice #{$invoice->invoice_number}: " . $e->getMessage());
        }
    }
}
