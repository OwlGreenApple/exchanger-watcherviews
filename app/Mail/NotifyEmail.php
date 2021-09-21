<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $invoice;
    // public $name;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        // $this->password = $password;
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
        ->view('emails.NotifyEmail')
        ->with([
          'password' => $this->password,
          'name' => $this->name,
        ]);
    }
}
