<?php

namespace App\Mail;

use App\Models\MsmeBusinessData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class BusinessDataMail extends Mailable
{
    use Queueable, SerializesModels;

    public MsmeBusinessData $businessData;
    public string $emailSubject;
    public ?string $customContent;
    public int $campaignItemId;

    /**
     * Create a new message instance.
     */
    public function __construct(
        MsmeBusinessData $businessData,
        string $emailSubject = 'GROW YOUR BUSINESS WITH MSME DIRECT',
        ?string $customContent = null,
        int $campaignItemId = 0
    ) {
        $this->businessData = $businessData;
        $this->emailSubject = $emailSubject;
        $this->campaignItemId = $campaignItemId;

        $encodedId = base64_encode((string) $businessData->id);
        $registerUrl = url('/register?id=' . $encodedId);
        if ($customContent) {
            $customContent = str_replace(
                ['{register_url}', '{id}', '{company}', '{person_name}'],
                [$registerUrl, $encodedId, $businessData->company ?? '', $businessData->person_name ?? ''],
                $customContent
            );
        }
        $this->customContent = $customContent;
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        $textHeaders = [];

        if ($this->campaignItemId > 0) {
            $textHeaders['X-Mailgun-Variables'] = json_encode([
                'campaign_item_id' => $this->campaignItemId,
            ]);
            $textHeaders['X-Campaign-Item-ID'] = (string) $this->campaignItemId;
        }

        return new Headers(
            text: $textHeaders,
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('register@msmedirect.com', 'MSME Direct'),
            replyTo: [
                new Address('msmedirectsales@gmail.com', 'MSME Direct'),
            ],
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business_campaign',
            with: [
                'businessData' => $this->businessData,
                'customContent' => $this->customContent,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
