<?php

namespace App\Http\Requests;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TopicUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $topic = $this->route('tema');

        return [
            'kind' => ['required', 'in:root,sub'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('topics', 'slug')->ignore($topic->id),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'required_if:kind,sub',
                Rule::exists('topics', 'id')->where(fn ($q) => $q->whereNull('parent_id')),
                Rule::notIn([$topic->id]),
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Topic $topic */
            $topic = $this->route('tema');
            if ($topic->children()->exists()) {
                if ($this->input('kind') === 'sub') {
                    $validator->errors()->add('kind', __('Un tema con subtemas debe seguir siendo tema principal.'));
                }
            }
        });
    }
}
