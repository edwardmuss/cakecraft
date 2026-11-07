<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $pdfPath;
    public $pdfName;

    public function __construct($payment, $pdfPath, $pdfName)
    {
        $this->payment = $payment;
        $this->pdfPath = $pdfPath;
        $this->$pdfName = $pdfName;
    }

    public function build()
    {
        return $this->subject('Your Payment Invoice')
            ->view('emails.payment-invoice')
            ->attach($this->pdfPath, [
                'as' => $this->pdfName,
                'mime' => 'application/pdf',
            ]);
    }
}
