<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\Price;

class UserBuyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $no;
    public $name;
    public $package;
    public $price;
    public $total;

    public function __construct($order,$name)
    {
        $this->order = $order;
        $this->name = $name;
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
        ->subject('Pembelian paket watchermarket')
        ->view('emails.UserBuyEmail')
        ->with([
          'order' => $this->order,
          'name' => $this->name,
          'pc' => new Price,
        ]);
    }
}
