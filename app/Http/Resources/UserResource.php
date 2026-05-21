<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @mixin \App\Models\User
 */
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
             'id' => (int)$this->resource ->id,
            'first_name' => (string)$this->resource->first_name,
            'last_name' => (string)$this->resource->last_name,
            'email' => (string)$this->resource->email,
            'phone' => (string)$this->resource->phone,
            'status' => (string)$this->resource->status,
            'user_type' => (string)$this->resource->user_type,
            'country' => (string) $this->resource->country?->name,
            'state' => (string)$this->resource->state,
            'address' => (string)$this->resource->address,
            'zip_code' => (string)$this->resource->zip_code,
            'lock_screen_enabled' => (bool)$this->resource->lock_screen_enabled,
            'biometric_enabled' => (bool)$this->resource->biometric_enabled,
            'kyc_verification' => (bool)$this->resource->kyc_verification,
            'two_factor_enabled' => (bool)$this->resource->two_factor_enabled,
            'profile_photo' => (string)$this->resource->profile_photo,
        ];
    }
}
