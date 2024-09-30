<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecieveCrypto extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $address;
    public $amount;
    public $link;
    public $token;

    public function __construct($address, $amount, $link, $token)
    {
        $this->address = $address;
        $this->amount = $amount;
        $this->link = $link;
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.ReceiveCrypto');
    }
}
