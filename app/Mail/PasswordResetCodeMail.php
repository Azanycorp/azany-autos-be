<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private User $user
    ) {}

    /**
     * Get the message envelope (Subject, From, etc.).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Reset Verification Code',
        );
    }

    /**
     * Get the message content definition (Markdown view).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.password-reset-code',
            with: [
                'user' => $this->user
            ],
        );
    }
}
