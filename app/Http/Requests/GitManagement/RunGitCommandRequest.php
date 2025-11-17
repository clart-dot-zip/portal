<?php

namespace App\Http\Requests\GitManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunGitCommandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['sync', 'status'])],
            'remote' => ['required', 'string', 'max:255'],
            'branch' => ['nullable', 'string', 'max:255', 'required_if:action,sync'],
        ];
    }

    public function gitCommand(): string
    {
        $action = $this->validated('action');
        $remote = $this->validated('remote');
        $branch = $this->validated('branch');

        switch ($action) {
            case 'sync':
                $remoteArg = escapeshellarg($remote ?? 'origin');
                $branchArg = escapeshellarg($branch ?? 'main');
                $remoteBranchArg = escapeshellarg(($remote ?? 'origin') . '/' . ($branch ?? 'main'));

                return sprintf(
                    'git fetch %1$s %2$s && git checkout -B %2$s %3$s && git pull %1$s %2$s',
                    $remoteArg,
                    $branchArg,
                    $remoteBranchArg
                );
            case 'status':
                return 'git status --short --branch';
            default:
                return 'git status --short --branch';
        }
    }

    public function commandMeta(): array
    {
        return [
            'action' => $this->validated('action'),
            'remote' => $this->validated('remote'),
            'branch' => $this->validated('branch'),
        ];
    }
}
