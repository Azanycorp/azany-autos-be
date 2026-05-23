<?php

namespace App\Http\Controllers;

use App\Http\Requests\CodeRequest;
use App\Http\Requests\UserResetPassRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private AuthService $accountService
    ) {}

    public function verifyUser(VerifyUserRequest $request): JsonResponse
    {
        return $this->accountService->verifyUserIdentity($request);
    }

    public function verifyCode(CodeRequest $request): JsonResponse
    {
        return $this->accountService->verifyCode($request);
    }

    public function changePassword(UserResetPassRequest $request): JsonResponse
    {
        return $this->accountService->changePassword($request);
    }

    public function resendCode(Request $request): JsonResponse
    {
        return $this->accountService->resendCode($request);
    }
}
