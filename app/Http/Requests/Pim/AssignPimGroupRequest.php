<?php

namespace App\Http\Requests\Pim;

use Illuminate\Foundation\Http\FormRequest;

class AssignPimGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pim_group_id' => ['required', 'integer', 'exists:pim_groups,id'],
        ];
    }
}
