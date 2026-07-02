<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class VehicleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
           'listing_type' => ['required', 'string', 'in:for_sale,for_rent'],
           'country_id' => ['required', 'integer', 'exists:countries,id'],
           'city' => ['required', 'string'],
           'fuel_type' => ['required', 'string', 'in:petrol,diesel,electric,hybrid'],
           'transmission_type' => ['required', 'string', 'in:manual,automatic'],
           'condition' => ['required', 'string', 'in:new,used'],
           'kilometer_reading' => ['required', 'integer'],
           'engine_capacity' => ['required', 'string'],
           'previous_owner' => ['nullable', 'string'],
           'make' => ['required', 'string'],
           'model' => ['required', 'string'],
           'year' => ['required', 'integer', 'digits:4'],
           'variant' => ['nullable', 'string'],
           'body_type' => ['required', 'string'],
           'vin' => ['required', 'string', 'unique:vehicles,vin'],
           'accident_history' => ['required', 'string', 'in:no_accidents,minor_accidents,major_accidents'],
           'damage_history' => ['required', 'string', 'in:no_damage,minor_damage,major_damage'],
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
        ];
    }
}
