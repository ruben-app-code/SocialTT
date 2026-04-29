<?php

namespace App\Http\Requests\Administrator;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('url');
        if ($this->input('blocked_at') === '') {
            $this->merge(['blocked_at' => null]);
        }
        $topics = $this->input('topics', []);
        $this->merge([
            'topics' => is_array($topics) ? $topics : [],
        ]);
    }

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
            'is_verified' => ['nullable', 'boolean'],
            'topics' => ['present', 'array'],
            'topics.*' => ['integer', 'exists:topics,id'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        /** @var \App\Models\User $user */
        $user = $this->route('user');
        /** @var \App\Models\SocialAccount $socialAccount */
        $socialAccount = $this->route('socialAccount');

        throw new HttpResponseException(
            redirect()->route('users.edit', $user)
                ->withErrors($validator)
                ->with('edit_social_account_error_id', $socialAccount->id)
        );
    }
}
