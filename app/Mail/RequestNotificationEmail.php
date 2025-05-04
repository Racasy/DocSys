<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RequestNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $requestTitle;
    public $deadline;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $requestTitle, $deadline)
    {
        $this->user = $user;
        $this->requestTitle = $requestTitle;
        $this->deadline = $deadline;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Document Request: ' . $this->requestTitle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.request_notification',
            with: [
                'user' => $this->user,
                'requestTitle' => $this->requestTitle,
                'deadline' => $this->deadline,
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