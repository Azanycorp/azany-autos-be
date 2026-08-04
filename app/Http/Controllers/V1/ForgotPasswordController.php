<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CodeRequest;
use App\Http\Requests\V1\UserResetPassRequest;
use App\Http\Requests\V1\VerifyUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private AuthService $account_service
    ) {}

    public function verifyUser(VerifyUserRequest $request): JsonResponse
    {
        return $this->account_service->verifyUserIdentity($request);
    }

    public function verifyCode(CodeRequest $request): JsonResponse
    {
        return $this->account_service->verifyCode($request);
    }

    public function changePassword(UserResetPassRequest $request): JsonResponse
    {
        return $this->account_service->changePassword($request);
    }

    public function resendCode(Request $request): JsonResponse
    {
        return $this->account_service->resendCode($request);
    }
}
