<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('url');
        if ($this->input('blocked_at') === '') {
            $this->merge(['blocked_at' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'social_network_id' => ['required', 'integer', 'exists:social_networks,id'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'current_status' => ['required', 'in:active,deleted,stolen,blocked'],
            'blocked_at' => ['nullable', 'date'],
            'block_duration_hours' => ['nullable', 'integer', 'min:1', 'max:8760'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
