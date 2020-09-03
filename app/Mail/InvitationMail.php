<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->subject = __('t.mails.invitation.subject', ['courseName' => $invitation->course->name]);
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
