<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property string $email
 * @property string $subject
 * @property string|null $body
 * @property string $mailable
 * @property string $status
 * @property array<string, mixed>|null $payload
 * @property int $attempts
 * @property int $max_attempts
 * @property string|null $scheduled_at
 * @property array<string, mixed>|null $error_response
 */

#[Fillable([
    'type',
    'email',
    'subject',
    'body',
    'mailable',
    'status',
    'attempts',
    'max_attempts',
    'scheduled_at',
    'error_response',
])]

class Mailing extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'error_response' => 'array',
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function saveData(mixed $data): self
    {
        $mail = new self;
        $mail->type = $data['type'];
        $mail->email = $data['email'];
        $mail->subject = $data['subject'];
        $mail->body = $data['body'];
        $mail->mailable = $data['mailable'];
        $mail->payload = $data['payload'];

        $mail->save();

        return $mail;
    }
}
