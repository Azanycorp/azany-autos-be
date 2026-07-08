<?php

use App\Http\Controllers\V1\AuthenticationController;
use App\Http\Controllers\V1\DealerController;
use App\Http\Controllers\V1\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::controller(AuthenticationController::class)
        ->group(function () {
            Route::post('/register', 'register');
            Route::post('/login', 'login');
            Route::post('/verify-otp', 'verifyOtp');
            Route::post('/verify-2fa', 'verify2fa');
            Route::post('/resendVerificationMail', 'resendVerificationEmail');
        });

    Route::controller(ForgotPasswordController::class)
        ->group(function () {
            Route::post('/verify-user', 'verifyUser');
            Route::post('/verify-code', 'verifyCode');
            Route::post('/reset-password', 'changePassword');
            Route::post('/resend-code', 'resendCode');
        });
});

Route::middleware(['auth:sanctum'])->prefix('dealer')->group(function () {
    Route::controller(DealerController::class)
        ->group(function () {
            Route::get('/profile/{user_id}', 'profile');

            Route::prefix('vehicles')->group(function () {
                Route::get('/', 'getVehicles');
                Route::post('/add', 'addVehicle');
                Route::get('/details/{id}', 'getVehicle');
                Route::put('/update/{id}', 'updateVehicle');
                Route::delete('/delete-vehicle/{id}', 'deleteVehicle');
                Route::put('/update-status/{id}', 'updateVehicleStatus');
                Route::delete('/delete-image/{id}', 'deleteVehicleImage');
            });

            Route::prefix('tags')->group(function () {
                Route::get('/{user_id}', 'getTags');
                Route::post('/add', 'addCustomTag');
                Route::get('/details/{id}', 'getTag');
                Route::put('/update/{id}', 'updateTag');
                Route::delete('/delete/{id}', 'deleteTag');
            });

            Route::prefix('location')->group(function () {
                Route::get('/{user_id}', 'getAllLocations');
                Route::post('/add', 'addNewLocation');
                Route::get('/details/{id}', 'getlocation');
                Route::put('/update/{id}', 'updateLocation');
                Route::put('/make-default/{id}', 'updateLocation');
                Route::delete('/delete/{id}', 'deleteLocation');
            });

        });

});
