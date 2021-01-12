<?php

namespace App\Mail;

use App\Model\Register;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class RequestReqA extends Mailable implements ShouldQueue {
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
            "type"  => $this->data["response"]["type"],
            "email" => $this->data["response"]["email"],
            "name"  => $this->data["response"]["name"],
            "date"  => $this->data["appointment"]["date"],
            "time"  => $this->data["appointment"]["time"],
        );
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
            ->subject("[Req] การขอนัดหมายการเจรจาธุรกิจ YOUR REQUEST FOR BUSINESS MATCHING")
            ->to($this->data["request"]["email"])
            ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
            ->view('email.ReqA', $data_rp);
    }
}
