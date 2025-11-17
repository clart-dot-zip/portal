<?php

namespace App\Http\Controllers;

use App\Http\Requests\GitManagement\RunGitCommandRequest;
use App\Models\GitManagedServer;
use App\Services\Pterodactyl\GitCommandExecutor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class GitManagementController extends Controller
{
    public function index()
    {
        $servers = GitManagedServer::with(['logs' => function ($query) {
            $query->latest()->limit(5);
        }, 'creator'])->orderBy('name')->get();

        return \view('git-management.index', [
            'servers' => $servers,
            'defaults' => [
                'remote' => \config('pterodactyl.default_remote', 'origin'),
                'branch' => \config('pterodactyl.default_branch', 'main'),
            ],
        ]);
    }

    public function runCommand(RunGitCommandRequest $request, GitManagedServer $server, GitCommandExecutor $executor)
    {
        $data = $request->validated();
        $remote = $data['remote'] ?: ($server->remote_name ?: \config('pterodactyl.default_remote', 'origin'));
        $branch = $data['branch'] ?: ($server->default_branch ?: \config('pterodactyl.default_branch', 'main'));
        $gitCommand = $this->compileGitCommand($data['action'], $remote, $branch, $data['custom_command'] ?? null);

        try {
            $result = $executor->runGitCommand(
                $server,
                $gitCommand,
                array_merge($request->commandMeta(), [
                    'resolved_remote' => $remote,
                    'resolved_branch' => $branch,
                ]),
                Auth::id()
            );

            $message = sprintf('Command "%s" %s on %s', $gitCommand, $result->successful ? 'completed successfully' : 'failed', $server->name);

            return \back()->with($result->successful ? 'success' : 'error', $message);
        } catch (Throwable $throwable) {
            Log::error('Git command execution failed', [
                'server_id' => $server->id,
                'command' => $gitCommand,
                'error' => $throwable->getMessage(),
            ]);

            return \back()->with('error', 'Git command failed: ' . $throwable->getMessage());
        }
    }

    private function compileGitCommand(string $action, string $remote, string $branch, ?string $custom = null): string
    {
        switch ($action) {
            case 'fetch':
                return sprintf('git fetch %s', escapeshellarg($remote));
            case 'pull':
                return sprintf('git pull %s %s', escapeshellarg($remote), escapeshellarg($branch));
            case 'checkout':
                return sprintf('git checkout %s', escapeshellarg($branch));
            case 'status':
                return 'git status --short --branch';
            case 'custom':
            default:
                return trim((string) $custom);
        }
    }
}
