<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


    Route::post('/run-migration', [HomeController::class, 'migrate']);

    Route::controller(AuthenticationController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/verify-2fa', 'verify2fa');
        Route::post('/resendVerificationMail', 'resendVerificationEmail');
    });

    Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
        Route::post('/verify-user', 'verifyUser');
        Route::post('/verify-code', 'verifyCode');
        Route::post('/reset-password', 'changePassword');
        Route::post('/resend-code', 'resendCode');

          Route::post('/verify-user', 'verifyUser');
        Route::post('/verify-code', 'verifyCode');
        Route::post('/reset-password', 'changePassword');
        Route::post('/resend-code', 'resendCode');
    });

  Route::controller(AuthenticationController::class)->group(function () {
        Route::post('/forgot-password', 'resetPassword');
        Route::post('/forgot-password-otp', 'verify');
        Route::post('/reset-password', 'reset');
        Route::post('/resendVerificationMail', 'resendVerificationEmail');
    });

   