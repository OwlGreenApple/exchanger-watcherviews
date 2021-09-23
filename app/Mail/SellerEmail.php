<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SellerEmail extends Mailable
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
        ->from('no-reply@exchanger.com', 'exchanger')
        ->subject($this->subject)
        ->view('emails.SellerEmail')
        ->with([
          'invoice' => $this->invoice,
          'url' => $this->url,
        ]);
    }
}
