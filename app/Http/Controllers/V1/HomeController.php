<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService) {}

    public function migrate(): JsonResponse
    {
        return $this->homeService->migrate();
    }
}
