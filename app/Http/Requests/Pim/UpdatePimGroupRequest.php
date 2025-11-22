<?php

namespace App\Http\Requests\Pim;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePimGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $group = $this->route('group');
        $groupId = $group ? $group->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:pim_groups,slug,' . $groupId],
            'description' => ['nullable', 'string', 'max:1000'],
            'default_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'min_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440', 'lte:default_duration_minutes'],
            'max_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440', 'gte:default_duration_minutes'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:pim_permissions,id'],
            'auto_approve' => ['sometimes', 'boolean'],
        ];
    }
}
