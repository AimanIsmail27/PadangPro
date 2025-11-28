<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName; // public property to use in the email

    /**
     * Create a new message instance.
     */
    public function __construct($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to PadangPro!')
                    ->view('emails.welcome'); // We'll create this view next
    }
}
