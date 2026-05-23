<?php

namespace App\Traits;

use App\Enum\MailingEnum;
use App\Mail\VerifyAccountMail;
use App\Models\User;
use App\Models\Verify;
use App\Notifications\ResetPassword;
use Exception;
use Illuminate\Support\Facades\Mail;

/**
 * Trait ShouldVerify
 *
 * @mixin User
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

    // public function sendVerificationEmail(): Verify
    // {
    //     $verifyUser = $this->generateVerifier();

    //     $expiresAt = $verifyUser->expires_at->format('H:ia jS F, Y');

    //     $content = <<<EOD
    //     Hello {$this->first_name},<br><br>

    //     Copy code below to verify account.<br><br>

    //     <strong>{$verifyUser->token}</strong><br><br>

    //     The code will expire at {$expiresAt}.<br><br>

    //     Thank you for using our application!
    //     EOD;

    //     Mail::html($content, function ($message) {
    //         $message->to($this->email)
    //             ->subject('Email Verification Request.');
    //     });

    //     return $verifyUser;
    // }

    public function sendVerificationEmail(): Verify
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;
        $type = MailingEnum::EMAIL_VERIFICATION;
        $subject = 'Email Verification Request.';
        $mail_class = VerifyAccountMail::class;

        $payload = [
        'user' => [
            'first_name' => $this->first_name,
            'token'      => $verifyUser->token,
            'expires_at' => $verifyUser->expires_at->toISOString(),
        ]
    ];

        mailSend($type, $user, $subject, $mail_class, $payload);

        return $verifyUser;
    }

    public function sendPasswordResetEmail(): Verify|string
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;
        try {
            $type = MailingEnum::RESET_OTP;
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
