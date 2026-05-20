<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class ChangePasswordRequest extends FormRequest
{
  /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string','min:6'],
            'password' => ['required', 'string', 'confirmed']
        ];
    }
}
