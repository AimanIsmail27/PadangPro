<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $userType; // staff or admin

    public function __construct($name, $userType)
    {
        $this->name = $name;
        $this->userType = $userType;
    }

    public function build()
    {
        $subject = $this->userType === 'administrator' 
                    ? 'Welcome to PadangPro as Administrator' 
                    : 'Welcome to PadangPro as Staff';

        return $this->subject($subject)
                    ->view('emails.staff_welcome'); // Blade for staff/admin
    }
}

