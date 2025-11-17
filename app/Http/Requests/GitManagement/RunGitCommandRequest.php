<?php

namespace App\Http\Requests\GitManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RunGitCommandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['fetch', 'pull', 'checkout', 'status', 'custom'])],
            'remote' => ['nullable', 'string', 'max:255', 'required_if:action,fetch,pull'],
            'branch' => ['nullable', 'string', 'max:255', 'required_if:action,pull,checkout'],
            'custom_command' => ['nullable', 'string', 'max:1024', 'required_if:action,custom'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('action') === 'custom') {
                $command = trim((string) $this->input('custom_command'));
                if ($command === '') {
                    $validator->errors()->add('custom_command', 'Please provide a git command to run.');
                } elseif (!str_starts_with($command, 'git ')) {
                    $validator->errors()->add('custom_command', 'Only git commands are permitted.');
                }
            }
        });
    }

    public function gitCommand(): string
    {
        $action = $this->validated('action');
        $remote = $this->validated('remote');
        $branch = $this->validated('branch');

        switch ($action) {
            case 'fetch':
                return sprintf('git fetch %s', escapeshellarg($remote ?? 'origin'));
            case 'pull':
                return sprintf('git pull %s %s', escapeshellarg($remote ?? 'origin'), escapeshellarg($branch ?? 'main'));
            case 'checkout':
                return sprintf('git checkout %s', escapeshellarg($branch ?? 'main'));
            case 'status':
                return 'git status --short --branch';
            case 'custom':
            default:
                return trim((string) $this->validated('custom_command'));
        }
    }

    public function commandMeta(): array
    {
        return [
            'action' => $this->validated('action'),
            'remote' => $this->validated('remote'),
            'branch' => $this->validated('branch'),
            'custom_command' => $this->validated('custom_command'),
        ];
    }
}
