<?php

namespace App\Mail;

use App\Models\ProjectEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Sent to the team when a visitor submits the quote form.
 */
class QuoteRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    /** Files larger than this are not attached; the email links to the dashboard instead. */
    private const MAX_ATTACHMENT_BYTES = 10 * 1024 * 1024;

    public function __construct(public ProjectEnquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New quote request: '.$this->enquiry->name.($this->enquiry->service ? ' · '.$this->enquiry->service : ''),
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-request',
            with: [
                'item' => $this->enquiry,
                'fileAttached' => $this->attachesFile(),
                'adminUrl' => route('admin.submissions.show', ['quotes', $this->enquiry->id]),
            ],
        );
    }

    public function attachments(): array
    {
        return $this->attachesFile()
            ? [Attachment::fromStorageDisk('local', $this->enquiry->attachment_path)->as($this->enquiry->attachment_name)]
            : [];
    }

    private function attachesFile(): bool
    {
        $path = $this->enquiry->attachment_path;

        return $path && Storage::disk('local')->exists($path) && Storage::disk('local')->size($path) <= self::MAX_ATTACHMENT_BYTES;
    }
}
