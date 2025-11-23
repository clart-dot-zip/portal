<?php

namespace App\Http\Requests\Pim;

use Illuminate\Foundation\Http\FormRequest;

class SelfActivatePimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pim_group_id' => ['required', 'integer', 'exists:pim_groups,id'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}
