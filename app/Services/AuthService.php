<?php

namespace App\Services;

use App\Enum\MailingEnum;
use App\Enum\UserStatus;
use App\Enum\UserType;
use App\Http\Requests\V1\CodeRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\ResetRequest;
use App\Http\Requests\V1\VerifyUserRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetCodeMail;
use App\Mail\TwoFACodeMail;
use App\Models\User;
use App\Models\Verify;
use App\Services\Auth\HttpService;
use App\Services\Auth\RequestOptions;
use App\Traits\HttpResponses;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class AuthService
{
    use HttpResponses;

    public function __construct(
        private readonly HttpService $httpService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $requestData = $request->only([
                'first_name',
                'last_name',
                'email',
                'country_id',
                'password',
            ]);

            $requestData['signed_up_from'] = 'Azanyautos';
            $requestData['type'] = $request->user_type == UserType::AUTOBUYER->value ? UserType::AUTOBUYER->value : UserType::AUTODEALER->value;

            $this->httpService->post('register', new RequestOptions(
                data: $requestData
            ));
            $currency_code = getCurrencyCodeByCountryId($request->country_id);

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'reg_number' => $request->reg_number,
                'business_name' => $request->business_name,
                'contact_person' => $request->contact_person,
                'country_id' => $request->country_id,
                'default_currency' => $currency_code,
                'status' => UserStatus::PENDING->value,
                'password' => bcrypt($request->password),
            ]);

            $user->sendVerificationEmail();

            return $this->successResponse(
                new UserResource($user),
                'Registration successful. Kindly check your inbox for instructions on how to verify your account. Thanks.'
            );

        } catch (RequestException $e) {
            return $this->errorResponse(null, $e->getMessage(), $e->response->status());

        } catch (\Exception $e) {
            return $this->errorResponse(null, "An error occured: {$e->getMessage()}", 400);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return $this->errorResponse(null, 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        if ($user->two_factor_enabled) {
            $verificationCode = generateUserVerificationCode();

            $user->update([
                'verification_code' => $verificationCode,
                'verification_code_expire_at' => now()->addMinutes(10),
            ]);

            $type = MailingEnum::TWO_FA_OTP->value;
            $subject = 'Two-Factor Authentication Code';
            $mail_class = TwoFACodeMail::class;
            $data = [
                'user' => $user,
            ];
            mailSend($type, $user, $subject, $mail_class, $data);

            return $this->successResponse(null, '2FA code sent.');
        }

        return $this->issueToken($user);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $verify = Verify::with('user')
                ->where('token', $request->code)
                ->where('expires_at', '>', now())
                ->first();

            if (! $verify) {
                return $this->errorResponse(null, 'Invalid or expired verification code.', 404);
            }

            $user = $verify->user;

            if (! $user) {
                return $this->errorResponse(null, 'Associated user account could not be found.', 404);
            }

            $user->update([
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE->value,
            ]);

            $verify->delete();

            $token = $user->createToken($user->email, ['*'], now()->addDays(5));

            $data = (object) [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
            ];

            return $this->successResponse($data, 'Account verified successfully.');
        } catch (\Throwable $th) {
            return $this->errorResponse(null, "An error occured: {$th->getMessage()}", 400);
        }
    }

    public function resetPassword(VerifyUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $result = $user->sendPasswordResetEmail();

        $message = ($result instanceof Verify) ? 'Reset email sent.' : $result;

        return $this->successResponse($message);
    }

    public function reset(ResetRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        try {
            $token = decrypt($request->token);
        } catch (\Exception $e) {
            return $this->errorResponse(null, 'Invalid token', 403);
        }

        $verify = Verify::where('email', $request->email)
            ->where('token', $token)
            ->first();

        if (! $verify) {
            return $this->errorResponse(null, 'Invalid or expired token', 403);
        }

        $user->verify($verify, $request->password);

        return $this->successResponse(null, 'Updated successfully');
    }

    public function resendVerificationEmail(VerifyUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (filled($user->email_verified_at)) {
            return $this->errorResponse(null, 'Email already verified.', 422);
        }

        $user->sendVerificationEmail();

        return $this->successResponse(null, 'Verification email resent.');
    }

    public function verify2fa(CodeRequest $request): JsonResponse
    {
        $user = User::where('verification_code', $request->verification_code)->first();

        if (! $user) {
            return $this->errorResponse(null, 'Invalid code entered, please try it again.', 403);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse(null, 'Verification Code has Expired!', 422);
        }

        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->issueToken($user);
    }

    protected function issueToken(User $user): JsonResponse
    {
        $token = $user->createToken($user->email);

        return $this->successResponse(
            [
                'user' => new LoginResource($user),
                'token' => $token->plainTextToken,
            ],
            'Token generated successfully.'
        );
    }

    // forgot password section
    public function resendCode(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $verificationCode = generateUserVerificationCode();
        $expiry = now()->addMinutes(10);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => $expiry,
        ]);

        $type = MailingEnum::RESET_OTP->value;
        $subject = 'Password Reset Request';
        $mail_class = PasswordResetCodeMail::class;
        $data = [
            'user' => $user,
        ];
        mailSend($type, $user, $subject, $mail_class, $data);

        return $this->successResponse(null, 'A new code has been sent to you');
    }

    public function verifyUserIdentity(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $verificationCode = generateUserVerificationCode();

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => Date::now()->addMinutes(30),
        ]);

        $type = MailingEnum::RESET_OTP->value;
        $subject = 'Password Reset Request';
        $mail_class = PasswordResetCodeMail::class;
        $data = [
            'user' => $user,
        ];

        mailSend($type, $user, $subject, $mail_class, $data);

        return $this->successResponse(null, 'A verification code has been sent to your email');
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $user = User::where('verification_code', $request->verification_code)->first();

        if (! $user) {
            return $this->errorResponse(null, 'Invalid code entered, please try it again.', 422);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse(null, 'Verification Code has Expired!', 422);
        }

        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->successResponse(null, 'Code Verified');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse(null, 'Password Reset successfully!');
    }
}
