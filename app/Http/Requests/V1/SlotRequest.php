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
           'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
           'location_id' => ['required', 'integer', 'exists:inspection_locations,id'],
           'inspection_date' => ['required','date','after_or_equal:today'],
           'inspection_time' => ['required'],
        ];
    }
}
