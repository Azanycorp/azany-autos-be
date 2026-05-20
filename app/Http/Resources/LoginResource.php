<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @mixin \App\Models\User
 */
class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'first_name' => (string)$this->first_name,
            'last_name' => (string)$this->last_name,
            'user_type' => (string)$this->user_type,
            'status' => (string)$this->status,
            'phone' => (string)$this->phone,
            'email' => (string)$this->email,
            'lock_screen_enabled' => (bool)$this->lock_screen_enabled,
            'biometric_enabled' => (bool)$this->biometric_enabled,
            'kyc_verification' => (bool)$this->kyc_verification,
            'two_factor_enabled' => (bool)$this->two_factor_enabled,
        ];
    }
}
