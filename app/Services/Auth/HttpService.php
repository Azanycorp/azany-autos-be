<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Client\ConnectionException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class HttpService
{
    public function __construct(private readonly Repository $repository) {}

    /**
     * @param array<string, mixed> $data
     */
    public function request(string $method, string $endpoint, array $data = [], ?string $token = null): Response
    {
        $client = Http::withHeaders([
            $this->repository->get('security.auth_header_key') => $this->repository->get('security.auth_header_value'),
        ]);

        if ($token) {
            $client = $client->withToken($token);
        }

        return match (strtolower($method)) {
            'get' => $client->get($endpoint, $data),
            'post' => $client->post($endpoint, $data),
            'put' => $client->put($endpoint, $data),
            'patch' => $client->patch($endpoint, $data),
            'delete' => $client->delete($endpoint, $data),
            default => throw new \InvalidArgumentException("Unsupported method [$method]"),
        };
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function sendRequest(string $method, string $endpoint, RequestOptions|array|null $options = null): Response
    {
        if (is_array($options)) {
            $options = new RequestOptions(data: $options);
        }

        $options = $options ?? new RequestOptions;

        try {
            $client = Http::withHeaders(array_merge([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ], $options->getHeaders()))
                ->timeout($options->getTimeout())
                ->connectTimeout($options->getConnectTimeout())
                ->retry(
                    $options->getRetries(),
                    $options->getRetryDelay(),
                    function (
                        Throwable $exception,
                    ): bool {
                        return $this->shouldRetry($exception);
                    }
                );

            if ($options->getToken()) {
                $client = $client->withToken($options->getToken());
            }

            return match (strtolower($method)) {
                'get' => $client->get($endpoint, $options->getData()),
                'post' => $client->post($endpoint, $options->getData()),
                'put' => $client->put($endpoint, $options->getData()),
                'patch' => $client->patch($endpoint, $options->getData()),
                'delete' => $client->delete($endpoint, $options->getData()),
                default => throw new \InvalidArgumentException("Unsupported method [$method]"),
            };

        } catch (Throwable $e) {
            logger()->error("HTTP Request Timeout: {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'timeout' => $options->getTimeout(),
            ]);

            $body = json_encode([
                'status' => false,
                'message' => "An error occured: {$e->getMessage()}, Timeout: {$options->getTimeout()}",
                'data' => null,
            ]) ?: '{"status":false,"message":"An error occurred","data":null}';

            return new Response(
                new GuzzleResponse(
                    status: 500,
                    headers: ['Content-Type' => 'application/json'],
                    body: $body
                )
            );
        }
    }

    private function shouldRetry(Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        $code = $exception->getCode();

        if ($code === 504) {
            return false;
        }

        return $code >= 500;
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function get(string $endpoint, RequestOptions|array|null $options = null): Response
    {
        return $this->sendRequest('GET', $endpoint, $options);
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function post(string $endpoint, RequestOptions|array|null $options = null): Response
    {
        return $this->sendRequest('POST', $endpoint, $options);
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function patch(string $endpoint, RequestOptions|array|null $options = null): Response
    {
        return $this->sendRequest('PATCH', $endpoint, $options);
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function put(string $endpoint, RequestOptions|array|null $options = null): Response
    {
        return $this->sendRequest('PUT', $endpoint, $options);
    }

    /**
     * @param RequestOptions|array<string, mixed>|null $options
     */
    public function delete(string $endpoint, RequestOptions|array|null $options = null): Response
    {
        return $this->sendRequest('DELETE', $endpoint, $options);
    }

    public function isSuccessful(Response $response): bool
    {
        return $response->successful();
    }
}
