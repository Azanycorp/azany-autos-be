<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
     public function migrate(): JsonResponse
    {
        Artisan::call('migrate', [
            '--force' => true
        ]);

        return new \Illuminate\Http\JsonResponse([
            'message' => 'Migration completed successfully.',
            'output' => Artisan::output(),
        ]);
    }
}
