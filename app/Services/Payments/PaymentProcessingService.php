<?php

namespace App\Services\Payments;

use App\DTOs\PaymentData;
use App\Mail\PaymentInvoiceMail;
use App\Models\Account;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\Billing\AccountLedgerService;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentProcessingService
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Process a top-up payment from any gateway
     */
    public function processTopUp(PaymentData $data): array
    {
        // 🔒 IDENTITY CHECK (gateway retries)
        if (Payment::where('method', $data->method)
            ->where('reference', $data->reference)
            ->exists()
        ) {
            return [
                'status' => true,
                'message' => 'Transaction already processed.',
                'receipt_number' => $data->reference,
                'response_type' => 'info',
            ];
        }
        $branch = Account::where('kra_pin_number', $data->accountNumber)->first();

        if (!$branch) {
            $this->notifyUnknownAccount($data);

            return $this->errorResponse(
                'Account not found for the provided reference.'
            );
        }

        return DB::transaction(function () use ($branch, $data) {

            // lock the account row so balance updates are consistent
            $branch = Account::whereKey($branch->id)->lockForUpdate()->first();

            $initialMinor = (int) $branch->balance_minor;

            $topupMinor = (int) round(((float) $data->amount) * 100);
            $newMinor   = $initialMinor + $topupMinor;

            // store payment
            $payment = Payment::create([
                'branch_id' => $branch->id,
                'amount'     => (float) $data->amount,      // KES
                'units'      => 0,                          // legacy field (no longer used)
                'method'     => $data->method,
                'reference'  => $data->reference,
                'type'       => 'top_up',
                'status'     => 'paid',
            ]);

            // billing ledger (reuse existing columns but store KES wallet amounts)
            // Billing::create([
            //     'branch_id'    => $branch->id,
            //     'created_by'    => User::SYSTEM_USER_ID,
            //     'initial_units' => round($initialMinor / 100, 2),  // store KES
            //     'new_units'     => round($newMinor / 100, 2),      // store KES
            //     'billing_type'  => ucfirst($data->method) . ' Top-up',
            //     'remarks'       => "Wallet top-up via {$data->method}. REF: {$data->reference}",
            // ]);

            app(AccountLedgerService::class)->apply(
                accountId: $branch->id,
                createdBy: User::SYSTEM_USER_ID,
                amountMinor: $topupMinor,
                currency: $branch->currency ?? 'KES',
                source: 'topup',
                eventType: 'topup',
                idempotencyKey: $data->reference,
                reference: $data->reference,
                description: "Wallet top-up KES {$data->amount} via {$data->method}. REF: {$data->reference}",
                meta: []
            );

            // update wallet balance
            // $branch->update([
            //     'balance_minor' => $newMinor,
            // ]);

            $this->logActivity($data, $branch);

            // update SMS message text to say KES, not units
            $this->sendSuccessSms($branch, (float) $data->amount, $data);

            $this->sendInvoiceEmail($payment);

            return $this->successResponse((float) $data->amount, $data->reference);
        });
    }

    /**
     * Send success SMS
     */
    protected function sendInvoiceEmail(Payment $payment): void
    {
        try {
            $payment = Payment::where('payments.id', $payment->id)
                ->join('accounts', 'payments.branch_id', '=', 'accounts.id')
                ->select(
                    'payments.*',
                    'accounts.branch_name',
                    'accounts.company_email',
                    'accounts.company_phone',
                    'accounts.kra_pin_number',
                    'accounts.sms_pricing'
                )
                ->first();

            if (!$payment || !$payment->company_email) {
                return;
            }

            $filename = "INV-RT" . sprintf("%04d", $payment->id)
                . "-" . now()->format('d-m-Y-His') . ".pdf";

            $pdfPath = storage_path("app/public/invoices/{$filename}");

            Pdf::loadView('pages.payments.invoice-download', compact('payment'))
                ->save($pdfPath);

            Mail::to($payment->company_email)
                ->queue(new PaymentInvoiceMail($payment, $pdfPath, $filename));

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        } catch (\Throwable $e) {
            Log::error('Invoice email failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id
            ]);
        }
    }

    /**
     * Send success SMS
     */
    protected function sendSuccessSms(Account $branch, float $amountKes, PaymentData $data): void
    {
        try {
            $this->smsService->sendSMS(
                $branch->company_phone,
                "Payment successful. KES {$amountKes} credited to {$branch->branch_name}. Receipt: {$data->reference}"
            );
        } catch (\Throwable $e) {
            Log::error('SMS failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify payer when account does not exist
     */
    protected function notifyUnknownAccount(PaymentData $data): void
    {
        if (!$data->senderPhone) {
            return;
        }

        try {
            $greeting = greetBasedOnTime();

            $this->smsService->sendSMS(
                $data->senderPhone,
                "{$greeting} {$data->senderName}, your payment of KES {$data->amount} has been received. REF: {$data->reference}"
            );
        } catch (\Throwable $e) {
            Log::error('Unknown account SMS failed: ' . $e->getMessage());
        }
    }

    /**
     * Activity log
     */
    protected function logActivity(PaymentData $data, Account $branch): void
    {
        ActivityLogger::log(
            "{$data->method} Payment Success",
            "Top-up KES {$data->amount}, REF: {$data->reference}",
            null,
            null,
            $branch->id
        );
    }

    protected function successResponse(float $amountKes, string $ref): array
    {
        return [
            'status'         => true,
            'message'        => "Transaction successful. KES {$amountKes} credited.",
            'receipt_number' => $ref,
            'response_type'  => 'success',
        ];
    }

    protected function errorResponse(string $message): array
    {
        return [
            'status'         => false,
            'message'        => $message,
            'response_type' => 'error',
        ];
    }
}
