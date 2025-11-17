<?php

namespace App\Http\Requests\GitManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGitManagedServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pterodactyl_server_id' => ['required', 'integer'],
            'pterodactyl_server_uuid' => ['required', 'string', Rule::unique('git_managed_servers', 'pterodactyl_server_uuid')],
            'pterodactyl_server_identifier' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'pterodactyl_node_id' => ['nullable', 'integer'],
            'pterodactyl_node_name' => ['nullable', 'string', 'max:255'],
            'container_name' => ['required', 'string', 'max:255'],
            'repository_path' => ['required', 'string', 'max:512'],
            'repository_url' => ['nullable', 'string', 'max:512'],
            'default_branch' => ['required', 'string', 'max:255'],
            'remote_name' => ['required', 'string', 'max:255'],
            'ssh_host' => ['required', 'string', 'max:255'],
            'ssh_port' => ['nullable', 'integer', 'between:1,65535'],
            'ssh_username' => ['required', 'string', 'max:255'],
            'ssh_private_key_path' => ['required', 'string', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'pterodactyl_server_uuid.unique' => 'This server has already been added to the portal.',
        ];
    }
}
