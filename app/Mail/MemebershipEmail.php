<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\Price;

class MemebershipEmail extends Mailable
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

    public function __construct($no,$name,$package,$price,$total)
    {
        $this->no = $no;
        $this->name = $name;
        $this->package = $package;
        $this->price = $price;
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
        ->view('emails.MembershipEmail')
        ->with([
          'no' => $this->no,
          'name' => $this->name,
          'package' => $this->package,
          'price' => $this->price,
          'total' => $this->total,
          'pc' => new Price,
        ]);
    }
}
