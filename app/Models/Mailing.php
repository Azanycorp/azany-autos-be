<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
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
