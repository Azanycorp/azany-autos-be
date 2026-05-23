<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\HomeService;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService) {}

    public function migrate(): JsonResponse
    {
        return $this->homeService->migrate();
    }
}
