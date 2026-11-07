<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactiveAccountNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $branch;
    public $isFirstTime;

    public function __construct($branch, $isFirstTime = false)
    {
        $this->account = $branch;
        $this->isFirstTime = $isFirstTime;
    }

    public function build()
    {
        return $this->subject('We Miss You – Log In to Your Account')
            ->view('emails.monthly.inactive_accounts');
    }
}
