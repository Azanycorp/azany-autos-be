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

    protected function withPagination(mixed $collection, ?string $message = null, int $code = Response::HTTP_OK, mixed $extraMeta = []): JsonResponse
    {
        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'data' => $collection->items(),
            'pagination' => [
                'current_page' => $collection->currentPage(),
                'last_page' => $collection->lastPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
                'prev_page_url' => $collection->previousPageUrl(),
                'next_page_url' => $collection->nextPageUrl(),
            ],
            'meta' => $extraMeta,
        ], $code);
    }
}
