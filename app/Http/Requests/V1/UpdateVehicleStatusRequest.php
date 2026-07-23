<?php

namespace App\Http\Requests\V1;

use App\Enum\VehicleStatus;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class UpdateVehicleStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(VehicleStatus::values())],
        ];
    }
}
