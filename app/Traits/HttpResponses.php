<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HttpResponses
{
    protected function successResponse(mixed $data, ?string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(mixed $data, ?string $message = null, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
