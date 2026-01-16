<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MatchRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $ad;
    public $statusLabel;

    public function __construct($application, $ad, string $statusLabel)
    {
        $this->application = $application;
        $this->ad = $ad;
        $this->statusLabel = $statusLabel; // Approved / Rejected
    }

    public function build()
    {
        $subject = "PadangPro: Your request has been {$this->statusLabel}";

        return $this->subject($subject)
            ->view('emails.matchmaking.request_status');
    }
}
