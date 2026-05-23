<?php

namespace App\Services\Email;

use App\Enum\MailingEnum;
use App\Models\Mailing;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailingService
{
    public function sendEmails(int $batchSize = 15): void
    {
        $emails = DB::transaction(fn () =>
            Mailing::where('status', MailingEnum::PENDING)
                ->whereRaw('attempts < max_attempts')
                ->limit($batchSize)
                ->lockForUpdate()
                ->get()
        );

        if ($emails->isEmpty()) {
            return;
        }

        foreach ($emails as $email) {
            try {
                if (! class_exists($email->mailable)) {
                    Log::error("Mailable class {$email->mailable} not found.");
                    $email->update([
                        'status' => MailingEnum::FAILED,
                        'error_response' => 'Mailable class not found',
                    ]);
                    continue;
                }

                $payload = (array) ($email->payload ?? []);

                if (blank($payload)) {
                    Log::error("Payload for mailable class {$email->mailable} is empty.");
                    $email->update([
                        'status' => MailingEnum::FAILED,
                        'error_response' => 'Payload is empty',
                    ]);
                    continue;
                }

                $mailableInstance = new $email->mailable(...$payload);

                // This runtime type assertion completely satisfies PHPStan without docblocks
                if (! $mailableInstance instanceof Mailable) {
                    Log::error("Class {$email->mailable} does not implement Mailable contract.");
                    $email->update([
                        'status' => MailingEnum::FAILED,
                        'error_response' => 'Invalid mailable class contract structure',
                    ]);
                    continue;
                }

                Mail::to($email->email)->send($mailableInstance);

                $email->update(['status' => MailingEnum::SENT]);

            } catch (Throwable $e) {
                Log::error('Email failed to send: ' . $e->getMessage());

                $email->increment('attempts');

                if ($email->attempts >= $email->max_attempts) {
                    $email->update([
                        'status' => MailingEnum::FAILED,
                        'error_response' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
