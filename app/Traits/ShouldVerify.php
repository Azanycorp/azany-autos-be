<?php

namespace App\Traits;

use App\Enum\MailingEnum;
use App\Mail\VerifyAccountMail;
use App\Models\User;
use App\Models\Verify;
use App\Notifications\ResetPassword;
use Carbon\CarbonInterface;
use Exception;

/**
 * Trait ShouldVerify
 *
 * @mixin User
 *
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $password
 * @property CarbonInterface|null $email_verified_at
 */
trait ShouldVerify
{
    private function generateVerifier(): Verify
    {
        $verificationCode = generateUserVerificationCode();

        return Verify::create([
            'user_id' => $this->id,
            'token' => $verificationCode,
            'email' => $this->email,
            'expires_at' => now()->addMinutes(180),
        ]);
    }

    public function sendVerificationEmail(): Verify
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;
        $type = MailingEnum::EMAIL_VERIFICATION->value;
        $subject = 'Email Verification Request.';
        $mail_class = VerifyAccountMail::class;

        $payload = [
            'user' => [
                'first_name' => $user->first_name,
                'token' => $verifyUser->token,
                'expires_at' => $verifyUser->expires_at->toISOString(),
            ],
        ];

        mailSend($type, $user, $subject, $mail_class, $payload);

        return $verifyUser;
    }

    public function sendPasswordResetEmail(): Verify|string
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;

        try {

            $type = MailingEnum::RESET_OTP->value;
            $subject = 'Password Reset Request';
            $mail_class = ResetPassword::class;
            $data = [
                'user' => $verifyUser,
            ];
            mailSend($type, $user, $subject, $mail_class, $data);

        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $verifyUser;
    }

    public function verify(Verify $verifier, string $password): void
    {
        $this->password = bcrypt($password);

        if (! $this->email_verified_at) {
            $this->email_verified_at = now();
        }

        $this->save();
        $verifier->delete();
    }
}
