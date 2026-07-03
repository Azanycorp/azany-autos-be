<?php

namespace App\Http\Requests\V1;

use App\Enum\AccidentType;
use App\Enum\ConditionType;
use App\Enum\DamageType;
use App\Enum\FuelType;
use App\Enum\ListingType;
use App\Enum\TransmissionType;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

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
            'auction_days' => ['nullable', 'integer'],
            'city' => ['required', 'string'],
            'fuel_type' => ['required', 'string', Rule::in(FuelType::values())],
            'transmission_type' => ['required', 'string', Rule::in(TransmissionType::values())],
            'condition' => ['required', 'string', Rule::in(ConditionType::values())],
            'kilometer_reading' => ['required', 'integer'],
            'engine_capacity' => ['required', 'string'],
            'previous_owner' => ['nullable', 'string'],
            'make' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year' => ['required', 'integer', 'digits:4'],
            'variant' => ['nullable', 'string'],
            'body_type' => ['required', 'string'],
            'vin' => ['required', 'string', 'unique:vehicles,vin'],
            'accident_history' => ['required', 'string', Rule::in(AccidentType::values())],
            'damage_history' => ['required', 'string', Rule::in(DamageType::values())],
            'service_history' => ['nullable', 'string'],
            'front_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'back_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'rear_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'passenger_side_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'dashboard_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'video_link' => ['nullable', 'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime', 'max:10240'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'features' => ['required', 'array'],
            'features.*' => ['string'],
            'vehicle_images' => ['required', 'array'],
            'vehicle_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];

    }
}
