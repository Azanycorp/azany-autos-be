<?php

namespace App\Traits;

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
        return response()->json([
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
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Return a custom paginated response.
     *
     * @param string $message
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function paginatedResponse($message, $resource, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource->items(),
            'meta' => [
                'current_page' => $resource->currentPage(),
                'total' => $resource->total(),
                'per_page' => $resource->perPage(),
                'last_page' => $resource->lastPage(),
            ]
        ], $statusCode);
    }
}
