<?php
namespace App\Http\Controllers;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request);
    }

    public function verify2fa(CodeRequest $request): JsonResponse
    {
        return $this->authService->verify2fa($request);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->authService->verifyOtp($request);
    }

    public function resetPassword(VerifyUserRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request);
    }

    public function reset(ResetRequest $request): JsonResponse
    {

        return $this->authService->reset($request);
    }

    public function resendVerificationEmail(VerifyUserRequest $request): JsonResponse
    {
        return $this->authService->resendVerificationEmail($request);
    }
}
