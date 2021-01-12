<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class InviteRegisterToBuyer extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public $register;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email) {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $data_rp = array(
            "email" => $this->email

        );
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
            ->subject("You are invited to join Online Business Matching")
            ->to($this->email)
            ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
            ->view('email.InviteBuyerToManageSlot', $data_rp);
    }
}
