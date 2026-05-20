<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFACodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * PHP 8.3 Constructor Property Promotion with strict typing.
     */
    public function __construct(
        public User $user,
        public string|int $code
    ) {}

    /**
     * Get the message envelope (Subject, From, etc.).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your 2FA Verification Code',
        );
    }

    /**
     * Get the message content definition (Markdown view).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.2fa-code',
            with: [
                'code' => $this->code,
                'user' => $this->user,
            ],
        );
    }
}
