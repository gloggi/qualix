<?php

namespace App\Mail;

use App\Models\Einladung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $einladung;

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct(Einladung $einladung)
    {
        $this->einladung = $einladung;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invitation');
    }
}
