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
        $this->baseUrl = config('services.auth_service_url');
        $this->timeout = 60;
        $this->request = $request;
    }

    public function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = []
    ): Response {

        $headerName = config('security.header_key');
        $securityHeader = [
            $headerName => $this->request->header($headerName)
        ];

        $clientHeaders = array_merge([
            'Accept' => 'application/json',
        ], $securityHeader, $headers);

        $client = Http::withHeaders($clientHeaders)
            ->timeout($this->timeout);

        return $client->{$method}($this->baseUrl . $endpoint, $data);
    }

    public function login(array $data, array $headers = []): Response
    {
        return $this->request('post', 'login', $data, $headers);
    }

    public function register(array $data, array $headers = []): Response
    {
        return $this->request('post', 'register', $data, $headers);
    }

    public function verifyCode(string $email, array $headers = []): Response
    {
        return $this->request('post', 'verify-code', ['email' => $email], $headers);
    }

    public function deleteUserAccount(string $email, array $headers = []): Response
    {
        return $this->request('delete', 'delete-account', ['email' => $email], $headers);
    }

    public function updatePassword(array $data): Response
    {
        return $this->request('post', 'change-password', $data);
    }

    public function updateProfile(array $data, array $headers = []): Response
    {
        return $this->request('patch', 'update-account', $data, $headers);
    }
}
