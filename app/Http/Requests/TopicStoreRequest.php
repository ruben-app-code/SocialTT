<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TopicStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kind' => ['required', 'in:root,sub'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:topics,slug'],
            'parent_id' => [
                'nullable',
                'integer',
                'required_if:kind,sub',
                Rule::exists('topics', 'id')->where(fn ($q) => $q->whereNull('parent_id')),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = trim((string) $this->input('slug', ''));
        if ($slug === '' && $this->filled('name')) {
            $slug = \Illuminate\Support\Str::slug($this->input('name'));
        }
        $this->merge(['slug' => $slug]);
        if ($this->input('kind') === 'root') {
            $this->merge(['parent_id' => null]);
        }
    }
}
