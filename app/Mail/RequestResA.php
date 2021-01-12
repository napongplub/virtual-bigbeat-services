<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class RequestResA extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public $data;

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
            "type"  => $this->data["request"]["type"],
            "email" => $this->data["request"]["email"],
            "name"  => $this->data["request"]["name"],
            "date"  => $this->data["appointment"]["date"],
            "time"  => $this->data["appointment"]["time"],
        );
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
            ->subject("[Res] การขอนัดหมายการเจรจาธุรกิจ YOUR HAVE GOT A REQUEST FOR BUSINESS MATCHING")
            ->to($this->data["response"]["email"])
            ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
            ->view('email.ResA', $data_rp);
    }
}
