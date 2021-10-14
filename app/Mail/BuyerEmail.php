<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuyerEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $invoice;
    public $url;

    public function __construct($invoice,$url)
    {
        $this->invoice = $invoice;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        ->subject($this->subject)
        ->view('emails.BuyerEmail')
        ->with([
          'invoice' => $this->invoice,
          'url' => $this->url,
        ]);
    }
}
