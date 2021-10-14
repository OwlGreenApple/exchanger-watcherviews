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
    public $url_confirm;
    public $url_dispute;

    public function __construct($invoice,$url_confirm,$url_dispute,$role)
    {
        $this->invoice = $invoice;
        $this->url_confirm = $url_confirm;
        $this->url_dispute = $url_dispute;
        $this->role = $role;
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
        ->view('emails.NotifyEmail')
        ->with([
          'invoice' => $this->invoice,
          'url_confirm' => $this->url_confirm,
          'url_dispute' => $this->url_dispute,
          'role' => $this->role,
        ]);
    }
}
