<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPaymentLink extends Mailable
{
    use Queueable, SerializesModels;
    public $maildata;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maildata)
    {
        $this->maildata = $maildata;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Msme Direct Payment Link',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sendPaymentLink',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
   public function build()
    {
        $email = $this->markdown('emails.sendPaymentLink')->with('maildata', $this->maildata);
        return $email;
    }
}
