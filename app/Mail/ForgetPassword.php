<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class ForgetPassword extends Mailable implements ShouldQueue {
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
            // "pass"  => Crypt::decryptString($this->data["p_hash"]),
            "pass"  => '2222',
        );
        // return $this->subject("การลงทะเบียนของคุณเสร็จสมบูรณ์แล้ว Your registration has been successfully completed")->to($this->register->email)->view('email.confirmation');
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
                    ->subject("Forget password to sign in virtual exhibition")
                    ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
                    ->to($this->data["email"])->view('email.forgetpassword', $data_rp);
    }
}
