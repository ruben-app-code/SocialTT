<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PollStoreRequest extends FormRequest
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
            'question' => ['required', 'string', 'max:500'],
            'type' => ['required', 'in:yes_no,multiple'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
            'yes_text' => [Rule::requiredIf(fn () => $this->input('type') === 'yes_no'), 'nullable', 'string', 'max:255'],
            'no_text' => [Rule::requiredIf(fn () => $this->input('type') === 'yes_no'), 'nullable', 'string', 'max:255'],
            'options' => [Rule::requiredIf(fn () => $this->input('type') === 'multiple'), 'nullable', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

        if ($this->input('type') === 'multiple' && is_array($this->input('options'))) {
            $filtered = array_values(array_filter(
                array_map('trim', $this->input('options', [])),
                fn (string $t) => $t !== ''
            ));
            $this->merge(['options' => $filtered]);
        }

        if ($this->input('type') === 'yes_no') {
            $this->merge([
                'yes_text' => trim((string) $this->input('yes_text', '')),
                'no_text' => trim((string) $this->input('no_text', '')),
            ]);
        }
    }
}
