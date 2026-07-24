<?php

namespace App\Http\Requests\V1;

use App\Enum\InspectionStatus;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class StatusUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(InspectionStatus::values())],
        ];
    }
}
