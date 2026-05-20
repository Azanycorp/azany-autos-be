<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait HttpResponses
{
    /**
     * Return a success JSON response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($message, $data = [], $statusCode = 200)
    {
           return new \Illuminate\Http\JsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $errors = [], $statusCode = 400)
    {
        return new \Illuminate\Http\JsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

   /**
     * Return a standardized paginated JSON response.
     *
     * @param string $message
     * @param LengthAwarePaginator<int, mixed> $resource  <-- FIX 1: Defined TKey (int) and TValue (mixed)
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function paginatedResponse(
        string $message,
        LengthAwarePaginator $resource,
        int $statusCode = 200
    ): JsonResponse {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'data'    => $resource->items(),
            'meta'    => [
                'current_page' => $resource->currentPage(),
                'total'        => $resource->total(),
                'per_page'     => $resource->perPage(),
                'last_page'    => $resource->lastPage(),
            ]
        ], $statusCode);
    }
}
