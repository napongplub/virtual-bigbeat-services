<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class NotifyVisitorMessage extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public $data;
    // public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $data_rp = array(
            "email" => $this->data["email"],
            "pass"  => Crypt::decryptString($this->data["p_hash"]),
            // "pass"  => '22',
            "fullname"  => $this->data["name"],
            "noRead" => $this->data["noRead"]
        );
        // return $this->subject("การลงทะเบียนของคุณเสร็จสมบูรณ์แล้ว Your registration has been successfully completed")->to($this->register->email)->view('email.confirmation');
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
                    ->subject("CEBIT ASEAN Thailand 2020 Virtual Edition: Unread message notification")
                    ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
                    ->to($this->data["email"])->view('email.notifyunreadvisitor', $data_rp);
    }
}
