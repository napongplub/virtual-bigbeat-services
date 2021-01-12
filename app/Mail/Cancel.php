<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class Cancel extends Mailable implements ShouldQueue {
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
            "ownerName"  => $this->data["ownerName"],
            "requestName" => $this->data["requestName"],
            "requestCompany"  => $this->data["requestCompany"],
            "date"  => $this->data["date"],
            "time"  => $this->data["time"]
        );
        return $this->from('cebit-2020@varpevent.com', 'CEBIT ASEAN THAILAND')
            ->subject("Your appointment request has been cancelled")
            ->to($this->data["email"])
            ->replyTo('info@cebitasean.com', 'CEBIT ASEAN THAILAND')
            ->view('email.cancel', $data_rp);
    }
}
