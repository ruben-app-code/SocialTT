<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PollUpdateRequest extends FormRequest
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
        $poll = $this->route('poll');

        return [
            'question' => ['required', 'string', 'max:500'],
            'type' => ['required', 'in:yes_no,multiple'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
            'yes_text' => [Rule::requiredIf(fn () => $this->input('type') === 'yes_no'), 'nullable', 'string', 'max:255'],
            'no_text' => [Rule::requiredIf(fn () => $this->input('type') === 'yes_no'), 'nullable', 'string', 'max:255'],
            'option_text' => [Rule::requiredIf(fn () => $this->input('type') === 'multiple'), 'nullable', 'array', 'min:2'],
            'option_text.*' => ['required', 'string', 'max:255'],
            'option_id' => ['nullable', 'array'],
            'option_id.*' => [
                'nullable',
                'integer',
                Rule::exists('poll_options', 'id')->where(fn ($q) => $q->where('poll_id', $poll->id)),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

        if ($this->input('type') === 'multiple' && is_array($this->input('option_text'))) {
            $texts = $this->input('option_text', []);
            $ids = $this->input('option_id', []);
            $newTexts = [];
            $newIds = [];
            foreach ($texts as $i => $t) {
                $t = trim((string) $t);
                if ($t === '') {
                    continue;
                }
                $newTexts[] = $t;
                $newIds[] = isset($ids[$i]) && $ids[$i] !== '' ? (int) $ids[$i] : null;
            }
            $this->merge(['option_text' => $newTexts, 'option_id' => $newIds]);
        }

        if ($this->input('type') === 'yes_no') {
            $this->merge([
                'yes_text' => trim((string) $this->input('yes_text', '')),
                'no_text' => trim((string) $this->input('no_text', '')),
            ]);
        }
    }
}
