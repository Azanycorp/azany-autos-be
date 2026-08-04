<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CodeRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\ResetRequest;
use App\Http\Requests\V1\VerifyUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(
        private readonly AuthService $auth_service
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->auth_service->register($request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->auth_service->login($request);
    }

    public function verify2fa(CodeRequest $request): JsonResponse
    {
        return $this->auth_service->verify2fa($request);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->auth_service->verifyOtp($request);
    }

    public function resetPassword(VerifyUserRequest $request): JsonResponse
    {
        return $this->auth_service->resetPassword($request);
    }

    public function reset(ResetRequest $request): JsonResponse
    {

        return $this->auth_service->reset($request);
    }

    public function resendVerificationEmail(VerifyUserRequest $request): JsonResponse
    {
        return $this->auth_service->resendVerificationEmail($request);
    }
}
