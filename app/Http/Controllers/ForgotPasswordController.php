<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Requests\UserResetPassRequest;
use App\Http\Requests\CodeRequest;

class ForgotPasswordController extends Controller
{
   public function __construct(
        private AuthService $accountService
    ) {}

    public function verifyUser(VerifyUserRequest $request)
    {
        return $this->accountService->verifyUserIdentity($request);
    }

    public function verifyCode(CodeRequest $request)
    {
        return $this->accountService->verifyCode($request);
    }
    
  public function changePassword(UserResetPassRequest $request)
    {
        return $this->accountService->changePassword($request);
    }

    public function resendCode(Request $request)
    {
        return $this->accountService->resendCode($request);
    }
}
