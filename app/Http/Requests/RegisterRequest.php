<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class RegisterRequest extends FormRequest
{
      /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'alpha_dash', 'max:255'],
            'last_name' => ['required', 'string', 'alpha_dash', 'max:255'],
            'user_type' => ['required', 'string','max:255','in:azanyauto_buyer,azanyauto_dealer'],
            'contact_person' => ['nullable', 'string','max:255'],
            'business_name' => ['nullable', 'string','max:255'],
            'reg_number' => ['nullable', 'string','max:255'],
            'email' => ['required', 'string', 'email', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
