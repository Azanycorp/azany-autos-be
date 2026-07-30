<?php

namespace App\Http\Requests\V1;

use App\Enum\ConditionType;
use App\Enum\FuelType;
use App\Enum\ListingType;
use App\Enum\TransmissionType;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class SavedSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'listing_type' => ['required', 'string', Rule::in(ListingType::values())],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'fuel_type' => ['required', 'string', Rule::in(FuelType::values())],
            'transmission_type' => ['required', 'string', Rule::in(TransmissionType::values())],
            'condition' => ['required', 'string', Rule::in(ConditionType::values())],
            'kilometer_reading' => ['required', 'integer'],
            'make' => ['required', 'string'],
            'name' => ['required', 'string', 'unique:saved_searches,name,except,id'],
            'model' => ['required', 'string'],
            'min_year' => ['required', 'integer', 'digits:4'],
            'max_year' => ['required', 'integer', 'digits:4'],
            'min_price' => ['required', 'numeric', 'min:0'],
            'max_price' => ['required', 'numeric', 'gte:min_price'],
            'body_type' => ['required', 'string'],
        ];
    }
}
