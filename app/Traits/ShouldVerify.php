<?php

namespace App\Traits;

use Exception;
use App\Models\Verify;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;

trait ShouldVerify
{
    private function generateVerifier(): Verify
    {
        return Verify::create([
            'user_id' => $this->id,
            'token' => rand(1000, 9999),
            'email' => $this->email,
            'expires_at' => now()->addMinutes(180)
        ]);
    }

    public function sendVerificationEmail()
    {
        $verifyUser = $this->generateVerifier();

        $expiresAt = $verifyUser->expires_at->format('H:ia jS F, Y');

        $content = <<<EOD
        Hello {$this->first_name},<br><br>

        Copy code below to verify account.<br><br>

        <strong>{$verifyUser->token}</strong><br><br>

        The code will expire at {$expiresAt}.<br><br>

        Thank you for using our application!
        EOD;

        Mail::html($content, function ($message) {
            $message->to($this->email)
                    ->subject('Email Verification Request.');
        });

        return $verifyUser;
    }

    public function sendPasswordResetEmail()
    {
        $verifyUser = $this->generateVerifier();
        try {
            $verifyUser->user->notify(new ResetPassword($verifyUser));
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $verifyUser;
    }


}
