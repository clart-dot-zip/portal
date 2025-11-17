<?php

namespace App\Http\Controllers;

use App\Http\Requests\GitManagement\RunGitCommandRequest;
use App\Models\GitManagedServer;
use App\Services\Pterodactyl\GitCommandExecutor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GitManagementController extends Controller
{
    public function index(GitCommandExecutor $executor)
    {
        $servers = GitManagedServer::with(['logs' => function ($query) {
            $query->latest()->limit(5);
        }, 'creator'])->orderBy('name')->get();

        $defaults = [
            'remote' => \config('pterodactyl.default_remote', 'origin'),
            'branch' => \config('pterodactyl.default_branch', 'main'),
            'ssh_user' => \config('pterodactyl.default_ssh_user'),
            'ssh_port' => \config('pterodactyl.default_ssh_port', 22),
        ];

        $servers->each(function (GitManagedServer $server) use ($executor, $defaults) {
            $cacheKey = $this->branchCacheKey($server);

            $branchMeta = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($executor, $server) {
                return $this->buildBranchMeta($executor, $server);
            });

            $server->setAttribute('current_branch', $branchMeta['current_branch'] ?? null);
            $server->setAttribute('available_branches', collect($branchMeta['branches'] ?? []));
            $server->setAttribute('branch_cache_timestamp', $branchMeta['cached_at'] ?? null);
        });

        return \view('git-management.index', [
            'servers' => $servers,
            'defaults' => $defaults,
        ]);
    }

    public function runCommand(RunGitCommandRequest $request, GitManagedServer $server, GitCommandExecutor $executor)
    {
        $data = $request->validated();
        $remote = $data['remote'] ?: ($server->remote_name ?: \config('pterodactyl.default_remote', 'origin'));
        $branch = $data['branch'] ?: ($server->default_branch ?: \config('pterodactyl.default_branch', 'main'));
        $gitCommand = $this->compileGitCommand($data['action'], $remote, $branch);

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

            Cache::forget($this->branchCacheKey($server));

            return \back()->with($result->successful ? 'success' : 'error', $message);
        } catch (Throwable $throwable) {
            Cache::forget($this->branchCacheKey($server));

            Log::error('Git command execution failed', [
                'server_id' => $server->id,
                'command' => $gitCommand,
                'error' => $throwable->getMessage(),
            ]);

            return \back()->with('error', 'Git command failed: ' . $throwable->getMessage());
        }
    }

    private function compileGitCommand(string $action, string $remote, string $branch): string
    {
        switch ($action) {
            case 'sync':
                $remoteArg = escapeshellarg($remote);
                $branchArg = escapeshellarg($branch);
                $remoteBranchArg = escapeshellarg($remote . '/' . $branch);

                return sprintf(
                    'git fetch %1$s %2$s && git checkout -B %2$s %3$s && git pull %1$s %2$s',
                    $remoteArg,
                    $branchArg,
                    $remoteBranchArg
                );
            case 'status':
                return 'git status --short --branch';
            case 'custom':
            default:
                return 'git status --short --branch';
        }
    }

    private function branchCacheKey(GitManagedServer $server): string
    {
        return sprintf('git-management:servers:%d:branch-meta', $server->id);
    }

    private function buildBranchMeta(GitCommandExecutor $executor, GitManagedServer $server): array
    {
        $currentBranch = null;
        $branchCandidates = collect();

        try {
            $currentBranchResult = $executor->probeGitCommand($server, 'git rev-parse --abbrev-ref HEAD');
            if ($currentBranchResult->successful) {
                $branchName = trim($currentBranchResult->output);
                if ($branchName !== '') {
                    $currentBranch = $branchName;
                    if ($branchName !== 'HEAD') {
                        $branchCandidates->push($branchName);
                    }
                }
            }
        } catch (Throwable $exception) {
            Log::warning('Failed to probe current branch', [
                'server_id' => $server->id,
                'error' => $exception->getMessage(),
            ]);
        }

        foreach ($this->branchProbeCommands() as $command => $transform) {
            try {
                $result = $executor->probeGitCommand($server, $command);
                if ($result->successful) {
                    $branches = $this->parseBranches($result->output, $transform);
                    $branchCandidates = $branchCandidates->merge($branches);
                }
            } catch (Throwable $exception) {
                Log::debug('Branch probe failed', [
                    'server_id' => $server->id,
                    'command' => $command,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $branches = $branchCandidates
            ->filter()
            ->map(fn ($branch) => Str::of($branch)->trim()->value())
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'current_branch' => $currentBranch,
            'branches' => $branches,
            'cached_at' => now()->toIso8601String(),
        ];
    }

    private function branchProbeCommands(): array
    {
        return [
            'git for-each-ref --format="%(refname:short)" refs/heads' => fn (string $branch) => $branch,
            'git for-each-ref --format="%(refname:short)" refs/remotes' => function (string $branch) {
                if (str_contains($branch, '->')) {
                    return null;
                }

                $parts = explode('/', $branch, 2);

                return $parts[1] ?? $parts[0];
            },
        ];
    }

    private function parseBranches(string $output, callable $transform): array
    {
        return collect(preg_split('/\r?\n/', $output))
            ->map(fn ($line) => $transform(trim($line)))
            ->filter()
            ->all();
    }
}
