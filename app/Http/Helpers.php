<?php

use App\Models\Mailing;
use App\Models\User;

if (! function_exists('mailSend')) {
    /**
     * @param array<string, mixed> $payloadData
     */
    function mailSend(string $type, User $recipient, string $subject, string $mailClass, array $payloadData = []): void
    {
        $data = [
            'type' => $type,
            'email' => $recipient->email,
            'subject' => $subject,
            'body' => '',
            'mailable' => $mailClass,
            'scheduled_at' => now(),
            'payload' => array_merge($payloadData),
        ];

        Mailing::saveData($data);
    }
}

if (! function_exists('generateUserVerificationCode')) {
    function generateUserVerificationCode(): int
    {
        return mt_rand(1000, 9999);
    }
}

if (! function_exists('userAuth')) {
    function userAuth(): ?User
    {
        return auth()->user();
    }
}
