<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisteredEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $password;
    public $name;
    public $destination;

    public function __construct($password,$name,$destination = null)
    {
        $this->name = $name;
        $this->password = $password;
        $this->destination = $destination;
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
        ->view('emails.RegisteredEmail')
        ->with([
          'password' => $this->password,
          'name' => $this->name,
          'destination' => $this->destination,
        ]);
    }
}
