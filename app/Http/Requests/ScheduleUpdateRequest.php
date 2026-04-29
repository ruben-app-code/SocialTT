<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'days' => ['required', 'array'],
            'days.*' => ['string', 'in:mon,tue,wed,thu,fri,sat,sun'],
            'time' => ['required', 'date_format:H:i'],
        ];
    }
}
