<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyPaymentStatementMail extends Mailable implements ShouldQueue
{
    public $branch;
    public $payments;
    public $pdfContent; // Stores base64-encoded PDF

    public function __construct($branch, $payments, $pdfContent)
    {
        $this->account = $branch;
        $this->payments = $payments;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        // Decode back to binary
        $pdfBinary = base64_decode($this->pdfContent);

        return $this->subject('Monthly Payment Statement - ' . now()->format('F Y'))
            ->markdown('emails.monthly.payment_statement')
            ->attachData($pdfBinary, 'payment_statement.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
