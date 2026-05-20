<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpService
{
    protected string $baseUrl;
    protected int $timeout;
    protected Request $request;

    public function __construct(Request $request)
    {
        /** @var string $baseUrl */
        $baseUrl = config('services.auth_service_url');

        $this->baseUrl = $baseUrl;
        $this->timeout = 60;
        $this->request = $request;
    }

    /**
     * Handle the outbound HTTP client requests.
     *
     * @param string $method
     * @param string $endpoint
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return Response
     */
    public function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = []
    ): Response {
        /** @var string $headerName */
        $headerName = config('security.header_key');

        $securityHeader = [
            $headerName => (string) $this->request->header($headerName)
        ];

        $clientHeaders = array_merge([
            'Accept' => 'application/json',
        ], $securityHeader, $headers);

        $client = Http::withHeaders($clientHeaders)
            ->timeout($this->timeout);

        /** @var Response */
        return $client->{$method}($this->baseUrl . $endpoint, $data);
    }

    /**
     * Authenticate user credentials.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return Response
     */
    public function login(array $data, array $headers = []): Response
    {
        return $this->request('post', 'login', $data, $headers);
    }

    /**
     * Create a new remote user record.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return Response
     */
    public function register(array $data, array $headers = []): Response
    {
        return $this->request('post', 'register', $data, $headers);
    }

    /**
     * Validate an account confirmation token.
     *
     * @param string $email
     * @param array<string, string> $headers
     * @return Response
     */
    public function verifyCode(string $email, array $headers = []): Response
    {
        return $this->request('post', 'verify-code', ['email' => $email], $headers);
    }

    /**
     * Purge a user record remotely.
     *
     * @param string $email
     * @param array<string, string> $headers
     * @return Response
     */
    public function deleteUserAccount(string $email, array $headers = []): Response
    {
        return $this->request('delete', 'delete-account', ['email' => $email], $headers);
    }

    /**
     * Modify account authentication strings.
     *
     * @param array<string, mixed> $data
     * @return Response
     */
    public function updatePassword(array $data): Response
    {
        return $this->request('post', 'change-password', $data);
    }

    /**
     * Mutate account profile configurations.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return Response
     */
    public function updateProfile(array $data, array $headers = []): Response
    {
        return $this->request('patch', 'update-account', $data, $headers);
    }
}
