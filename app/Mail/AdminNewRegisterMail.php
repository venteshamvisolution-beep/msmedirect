<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminNewRegisterMail extends Mailable
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Registration Received – MSME Direct'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-register'
        );
    }
}
