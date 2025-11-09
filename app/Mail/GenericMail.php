<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string  $title,
        public string  $body,
        public ?string $attachmentPath = null,
        public array   $extraAttachments = [],
        public string  $disk = 'local',
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.generic-mail',
            with: [
                'body' => $this->body,
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachmentPath) {
            $attachments[] = Attachment::fromStorageDisk($this->disk, 'email/attachments/' . $this->attachmentPath);
        }
        foreach ($this->extraAttachments as $relPath) {
            $attachments[] = Attachment::fromStorageDisk($this->disk, $relPath);
        }

        return $attachments;
    }


}
