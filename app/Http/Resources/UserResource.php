<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'status' => (string)$this->status,
            'user_type' => (string)$this->user_type,
            'country' => (string)$this->country?->name,
            'state' => (string)$this->state,
            'address' => (string)$this->address,
            'zip_code' => (string)$this->zip_code,
            'lock_screen_enabled' => (bool)$this->lock_screen_enabled,
            'biometric_enabled' => (bool)$this->biometric_enabled,
            'kyc_verification' => $this->kyc_verification,
            'two_factor_enabled' => $this->two_factor_enabled,
            'profile_photo' => (string)$this->profile_photo,
        ];
    }
}
