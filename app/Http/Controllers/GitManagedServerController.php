<?php

namespace App\Http\Controllers;

use App\Http\Requests\GitManagement\StoreGitManagedServerRequest;
use App\Models\GitManagedServer;
use App\Services\Pterodactyl\PterodactylClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GitManagedServerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'portal.admin:false', 'pim.permission:git.manage']);
    }

    public function create(Request $request, PterodactylClient $client)
    {
        try {
            $servers = $client->listServers($request->boolean('refresh'));
        } catch (RuntimeException $runtimeException) {
            Log::error('Pterodactyl client misconfigured', ['error' => $runtimeException->getMessage()]);

            return \redirect()
                ->route('git-management.index')
                ->with('error', 'Pterodactyl API credentials are not configured.');
        } catch (Throwable $throwable) {
            Log::error('Failed to fetch servers from Pterodactyl', ['error' => $throwable->getMessage()]);

            return \redirect()
                ->route('git-management.index')
                ->with('error', 'Unable to contact the Pterodactyl API: ' . $throwable->getMessage());
        }

        $existing = GitManagedServer::pluck('pterodactyl_server_uuid')->all();
        $defaults = [
            'remote' => \config('pterodactyl.default_remote', 'origin'),
            'branch' => \config('pterodactyl.default_branch', 'main'),
            'repository_path' => \config('pterodactyl.default_repository_path', '/home/container'),
            'ssh_user' => \config('pterodactyl.default_ssh_user'),
            'ssh_port' => \config('pterodactyl.default_ssh_port', 22),
            'ssh_key_path' => \config('pterodactyl.default_ssh_key_path'),
        ];

        return \view('git-management.add', compact('servers', 'existing', 'defaults'));
    }

    public function store(StoreGitManagedServerRequest $request)
    {
        $data = $request->validated();
        $data['remote_name'] = $data['remote_name'] ?: \config('pterodactyl.default_remote', 'origin');
        $data['default_branch'] = $data['default_branch'] ?: \config('pterodactyl.default_branch', 'main');
        $data['created_by'] = Auth::id();

        GitManagedServer::create($data);

        return \redirect()->route('git-management.index')->with('success', 'Server added to Git Management.');
    }

    public function destroy(GitManagedServer $server)
    {
        $name = $server->name;
        $server->delete();

        return \redirect()->route('git-management.index')->with('success', 'Removed ' . $name . ' from Git Management.');
    }
}
