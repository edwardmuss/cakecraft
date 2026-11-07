<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyMessagesSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $branch;
    public $messagesByStatus;

    public function __construct($branch, $messagesByStatus)
    {
        $this->account = $branch;
        $this->messagesByStatus = $messagesByStatus;
    }

    public function build()
    {
        return $this->subject('Monthly SMS Summary - ' . now()->format('F Y'))
            ->markdown('emails.monthly.messages_summary');
    }
}
