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
    public $trans_id;
    public $coin;
    public $total;

    public function __construct($invoice,$url,$trans_id = null,$coin = null,$total = null)
    {
        $this->invoice = $invoice;
        $this->url = $url;
        $this->trans_id = $trans_id;
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
        ->view('emails.SellerEmail')
        ->with([
          'invoice' => $this->invoice,
          'url' => $this->url,
          'trans_id' => $this->trans_id,
          'coin' => $this->coin,
          'total' => $this->total,
        ]);
    }
}
