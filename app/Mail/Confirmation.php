<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class Confirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $register;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Register $register)
    {
        $this->register = $register;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data_rp = array(
            "email" => $this->register->email,
            "pass"  => Crypt::decryptString($this->register->p_hash),
        );
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
                    ->subject("การลงทะเบียนของคุณเสร็จสมบูรณ์แล้ว Your registration has been successfully completed")
                    ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
                    ->to($this->register->email)->view('email.confirmation', $data_rp);
    }
}
