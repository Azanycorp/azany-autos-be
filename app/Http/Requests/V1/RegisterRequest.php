<?php

namespace App\Http\Requests\V1;

use App\Enum\UserType;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

#[FailOnUnknownFields]
class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'alpha_dash', 'max:50'],
            'last_name' => ['required', 'string', 'alpha_dash', 'max:50'],
            'user_type' => ['required', 'string', 'max:50', Rule::in(UserType::values())],
            'contact_person' => ['nullable', 'string', 'max:50'],
            'business_name' => ['nullable', 'string', 'max:50'],
            'reg_number' => ['nullable', 'string', 'max:50', 'unique:users,reg_number'],
            'email' => ['required', 'string', 'email', 'email:rfc,dns', 'max:50', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
