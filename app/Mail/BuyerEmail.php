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
    public $coin;
    public $total;

    public function __construct($invoice,$url,$coin = null,$total = null)
    {
        $this->invoice = $invoice;
        $this->url = $url;
        $this->coin = $coin;
        $this->total = $total;
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
          'coin' => $this->coin,
          'total' => $this->total,
        ]);
    }
}
