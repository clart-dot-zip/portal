<?php

namespace App\Services\Pterodactyl;

use App\Models\GitManagedServer;
use App\Models\GitOperationLog;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
use RuntimeException;
use Throwable;

class GitCommandExecutor
{
    /**
     * Execute a git command inside the target container and persist the log.
     */
    public function runGitCommand(GitManagedServer $server, string $gitCommand, array $parameters = [], ?int $userId = null): GitCommandResult
    {
        $log = new GitOperationLog([
            'git_managed_server_id' => $server->id,
            'executed_by' => $userId,
            'action' => $parameters['action'] ?? 'custom',
            'command' => $gitCommand,
            'parameters' => $parameters,
            'status' => 'pending',
        ]);
        $log->save();

        try {
            $log->update(['status' => 'running']);

            $ssh = $this->establishConnection($server);
            $dockerCommand = $this->buildDockerCommand($server, $gitCommand);

            Log::info('Executing git command through SSH', [
                'server_id' => $server->id,
                'command' => $dockerCommand,
            ]);

            $output = $ssh->exec($dockerCommand . ' 2>&1');
            $exitCode = $ssh->getExitStatus();

            $result = new GitCommandResult($exitCode === 0, $exitCode ?? 0, $output ?? '');

            $log->status = $result->successful ? 'succeeded' : 'failed';
            $log->output = $result->output;
            $log->error_output = $result->successful ? null : $result->output;
            $log->save();

            return $result;
        } catch (Throwable $exception) {
            Log::error('Failed to execute git command on Pterodactyl server', [
                'server_id' => $server->id,
                'error' => $exception->getMessage(),
            ]);

            $log->status = 'failed';
            $log->error_output = $exception->getMessage();
            $log->save();

            throw $exception;
        }
    }

    /**
     * Build the docker exec invocation that runs git inside the container.
     */
    private function buildDockerCommand(GitManagedServer $server, string $gitCommand): string
    {
    $repositoryPath = $server->repository_path ?: \config('pterodactyl.default_repository_path');
        if (empty($repositoryPath)) {
            throw new RuntimeException('Repository path is not configured for this server.');
        }

        $target = $server->docker_exec_target;
        $innerCommand = sprintf('cd %s && %s', escapeshellarg($repositoryPath), $gitCommand);

        return sprintf('docker exec -i %s sh -c %s', escapeshellarg($target), escapeshellarg($innerCommand));
    }

    /**
     * Establish an SSH session either by private key or password authentication.
     */
    private function establishConnection(GitManagedServer $server): SSH2
    {
        $host = $server->ssh_host;
    $port = $server->ssh_port ?: (int) \config('pterodactyl.default_ssh_port', 22);
    $username = $server->ssh_username ?: \config('pterodactyl.default_ssh_user');
    $keyPath = $server->ssh_private_key_path ?: \config('pterodactyl.default_ssh_key_path');

        if (empty($host) || empty($username)) {
            throw new RuntimeException('SSH host or username is not defined for this managed server.');
        }

        $ssh = new SSH2($host, $port);

        if (!empty($keyPath)) {
            if (!is_readable($keyPath)) {
                throw new RuntimeException(sprintf('SSH private key is not readable at path: %s', $keyPath));
            }

            $keyContents = file_get_contents($keyPath);
            if ($keyContents === false) {
                throw new RuntimeException(sprintf('Unable to read SSH private key: %s', $keyPath));
            }

            $key = PublicKeyLoader::load($keyContents);
            if (!$ssh->login($username, $key)) {
                throw new RuntimeException('SSH authentication failed using the provided private key.');
            }
        } else {
            throw new RuntimeException('No SSH authentication method configured. Provide a private key path.');
        }

        return $ssh;
    }
}
