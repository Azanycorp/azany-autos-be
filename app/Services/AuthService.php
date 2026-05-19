<?php

namespace App\Services;

use App\Enum\UserStatus;
use App\Http\Resources\Api\LoginResource;
use App\Http\Resources\UserResource;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use App\Services\Auth\HttpService;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use HttpResponses;

    public function __construct(
        private HttpService $httpService
    ) {}

    public function register($request)
    {
        $requestData = $request->only([
            'first_name',
            'last_name',
            'email',
            'country_id',
            'password',
        ]);

        $requestData['signed_up_from'] = 'Azanyautos';
        $requestData['type'] = 'azanyautos_buyer';

        $response = $this->httpService->register($requestData);
        if ($response->successful()) {

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'country_id' => $request->country_id,
                'status' => UserStatus::PENDING->value,
                'password' => bcrypt($request->password),
            ]);

            $user->sendVerificationEmail();

            return $this->successResponse(
                'Registration successful. Kindly check your inbox for instructions on how to verify your account. Thanks.',
                new UserResource($user),
                Response::HTTP_CREATED
            );
        } else {
            return $this->errorResponse($response->json(), [], $response->status());
        }
    }

    public function login($request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credentials do not match'], 401);
        }
        
        $response = $this->httpService->login($credentials);

        if ($response->successful()) {
            $user = User::where('email', $request->email)->first();
            if ($user->two_factor_enabled) {
                $verificationCode = mt_rand(1000, 9999);
                $expiry = now()->addMinutes(30);

                $user->update([
                    'verification_code' => $verificationCode,
                    'verification_code_expire_at' => $expiry,
                ]);

                Mail::to($user->email)->send(new TwoFactorCodeMail($user, $verificationCode));

                return $this->successResponse('2FA code sent. Please verify.', [
                    'email' => $user->email,
                    '2fa_required' => true,
                ], Response::HTTP_ACCEPTED);
            }

            return $this->issueToken($user,$request->email);

        } elseif ($response->status() === 401) {

            $user = User::where('email', $request->email)->first();

            if ($user) {

                $creationResponse = $this->httpService->register(
                    [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'country_id' => $user->country_id,
                        'password' => $request->password,
                        'signed_up_from' => 'Azanyautos',
                        'type' => 'azanyautos_buyer',
                        'email_verified_at' => now(),
                        'is_verified' => true,
                        'status' => UserStatus::ACTIVE->value,
                    ]
                );

                if ($creationResponse->successful()) {

                     $reloginResponse = $this->httpService->login($credentials);

                    if ($reloginResponse->successful()) {
                        return $this->issueToken($user, $request->email);
                    } else {
                        return $this->errorResponse('Login failed, please try again.', [], $reloginResponse->status());
                    }
                } else {
                     return $this->errorResponse(
                        $creationResponse->json()['message'] ?? 'Failed to create account on auth service.',
                        [],
                        $creationResponse->status()
                    );
                }
            }

            return $this->errorResponse('Invalid credentials or user does not exist.', [], Response::HTTP_UNAUTHORIZED);
        } else {
            return $this->errorResponse($response->json()['message'], [], $response->status());
        }
    }

    protected function issueToken($user)
    {

        $token = $user->createToken(
            $user->email,
            ['*']
        );

        return response()->json([
            'user' => new LoginResource($user),
            'token' => $token->plainTextToken,
        ]);
    }
}
