<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
   public function rules(): array
{
    return [
        'first_name' => ['sometimes', 'required', 'string', 'max:255'],
        'last_name'  => ['sometimes', 'required', 'string', 'max:255'],
        'email'      => [
            'sometimes',
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($this->user()?->id),
        ],
        'country_id' => ['sometimes', 'required', 'integer', 'exists:countries,id'],
    ];
}
}
