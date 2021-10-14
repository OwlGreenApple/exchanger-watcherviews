<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WarningEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $invoice;

    public function __construct($invoice,$pos = null,$subject_mail)
    {
        $this->invoice = $invoice;
        $this->pos = $pos;
        $this->subject_mail = $subject_mail;
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
        ->subject($this->subject_mail)
        ->view('emails.WarningEmail')
        ->with([
          'invoice' => $this->invoice,
          'pos' => $this->pos,
          'subject' => $this->subject_mail,
        ]);
    }
}
