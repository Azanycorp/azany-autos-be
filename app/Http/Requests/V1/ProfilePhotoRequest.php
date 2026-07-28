<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class ProfilePhotoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
        ];
    }
}
