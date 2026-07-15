<?php

namespace App\Http\Requests\V1;

use App\Enum\FuelType;
use App\Enum\TransmissionType;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class PreferenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_ids' => ['required', 'array', 'exists:vehicles,id'],
            'prefered_colors' => ['required', 'array'],
            'body_types' => ['required', 'array'],
            'fuel_types' => ['required', 'array', Rule::in(FuelType::values())],
            'transmissions' => ['required', 'array', Rule::in(TransmissionType::values())],
            'budget_min' => ['required'],
            'budget_max' => ['required'],
        ];
    }
}
