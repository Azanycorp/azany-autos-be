<?php

use App\Http\Controllers\V1\HomeController;
use Illuminate\Support\Facades\Route;

Route::post('/run-migration', [HomeController::class, 'migrate']);

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1.php';
});
