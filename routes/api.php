<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


    Route::post('/run-migration', [HomeController::class, 'migrate']);

    Route::controller(AuthenticationController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::post('/verify', 'verify')->name('verify');
        Route::post('/verify-2fa', 'verify2fa');
        Route::post('/resendVerificationMail', 'resendVerificationEmail')->name('resendVerificationEmail');
    });

    Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
        Route::post('/verify-user', 'verifyUser');
        Route::post('/verify-code', 'verifyCode');
        Route::post('/reset-password', 'changePassword');
        Route::post('/resend-code', 'resendCode');
    });

