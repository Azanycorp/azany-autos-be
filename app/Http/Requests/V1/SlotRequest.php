<?php

namespace App\Http\Requests\V1;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class SlotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
           'vehicle_id' => ['required', 'string', 'exists:vehicles,id'],
           'location_id' => ['required', 'string', 'exists:inspection_locations,name'],
           'inspection_date' => ['required','date'],
           'inspection_time' => ['required','timezone'],

        ];
    }
}
