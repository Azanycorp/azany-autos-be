<?php

namespace App\Services;

use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class HomeService
{
    use HttpResponses;

    public function migrate(): JsonResponse
    {
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        return $this->successResponse(['output' => Artisan::output()], 'Migration completed successfully.');
    }
}
