<?php

namespace App\Http\Requests\Pim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePimGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:pim_groups,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'default_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'min_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440', 'lte:default_duration_minutes'],
            'max_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440', 'gte:default_duration_minutes'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:pim_permissions,id'],
            'auto_approve' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $providedSlug = (string) $this->input('slug', '');
        $slugSource = trim($providedSlug) !== '' ? $providedSlug : (string) $this->input('name', '');
        $slug = Str::slug($slugSource);

        if ($slug === '') {
            $slug = Str::slug('pim-group-' . Str::random(6));
        }

        $this->merge([
            'slug' => $slug,
        ]);
    }
}
