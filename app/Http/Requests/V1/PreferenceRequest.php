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
            'vehicle_ids' => ['required', 'array'],
            'prefered_colors' => ['required', 'array'],
            'body_types' => ['required', 'array'],
            'fuel_types' => ['required', 'array'],
            'transmissions' => ['required', 'array'],
            'vehicle_ids.*' => ['integer', 'exists:vehicles,id'],
            'prefered_colors.*' => ['string'],
            'body_types.*' => ['string'],
            'fuel_types.*' => [Rule::in(FuelType::values())],
            'transmissions.*' => [Rule::in(TransmissionType::values())],
            'budget_min' => ['required', 'numeric', 'min:0'],
            'budget_max' => ['required', 'numeric', 'gte:budget_min'],
        ];
    }
}
