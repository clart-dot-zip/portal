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

            [$result, $containerTarget] = $this->execute($server, $gitCommand, true);

            $log->update([
                'status' => $result->successful ? 'succeeded' : 'failed',
                'output' => $result->output,
                'error_output' => $result->successful ? null : $result->output,
                'parameters' => array_merge((array) $log->parameters, [
                    'container_target' => $containerTarget,
                ]),
            ]);

            return $result;
        } catch (Throwable $exception) {
            Log::error('Failed to execute git command on Pterodactyl server', [
                'server_id' => $server->id,
                'error' => $exception->getMessage(),
            ]);

            $log->update([
                'status' => 'failed',
                'error_output' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Execute a git command without logging a persistent record. Useful for probes.
     */
    public function probeGitCommand(GitManagedServer $server, string $gitCommand): GitCommandResult
    {
        [$result] = $this->execute($server, $gitCommand, false);

        return $result;
    }

    /**
     * Build the docker exec invocation that runs git inside the container.
     */
    private function buildDockerCommand(string $containerTarget, GitManagedServer $server, string $gitCommand): string
    {
        $repositoryPath = $server->repository_path ?: \config('pterodactyl.default_repository_path');
        if (empty($repositoryPath)) {
            throw new RuntimeException('Repository path is not configured for this server.');
        }

        $innerCommand = sprintf('cd %s && %s', escapeshellarg($repositoryPath), $gitCommand);

        return sprintf('docker exec -i %s sh -c %s', escapeshellarg($containerTarget), escapeshellarg($innerCommand));
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

    /**
     * Resolve the correct container name by probing docker for candidate identifiers.
     */
    private function resolveContainerTarget(SSH2 $ssh, GitManagedServer $server): string
    {
        $candidates = array_values(array_unique(array_filter([
            $server->container_name,
            $server->pterodactyl_server_uuid,
            $server->pterodactyl_server_identifier
                ? 'pterodactyl_' . $server->pterodactyl_server_identifier
                : null,
        ])));

        if (empty($candidates)) {
            throw new RuntimeException('No container candidates available for this server.');
        }

        foreach ($candidates as $candidate) {
            $ssh->exec(sprintf('docker container inspect %s >/dev/null 2>&1', escapeshellarg($candidate)));
            $exitCode = $ssh->getExitStatus();
            if ($exitCode === 0) {
                return $candidate;
            }
        }

        throw new RuntimeException('Unable to locate a matching container. Tried: ' . implode(', ', $candidates));
    }

    /**
     * Execute the provided git command inside the target container.
     */
    private function execute(GitManagedServer $server, string $gitCommand, bool $logExecution): array
    {
        $ssh = $this->establishConnection($server);
        $containerTarget = $this->resolveContainerTarget($ssh, $server);
        $dockerCommand = $this->buildDockerCommand($containerTarget, $server, $gitCommand);

        if ($logExecution) {
            Log::info('Executing git command through SSH', [
                'server_id' => $server->id,
                'command' => $dockerCommand,
                'container_target' => $containerTarget,
            ]);
        } else {
            Log::debug('Probing git command through SSH', [
                'server_id' => $server->id,
                'command' => $dockerCommand,
                'container_target' => $containerTarget,
            ]);
        }

        $output = $ssh->exec($dockerCommand . ' 2>&1');
        $exitCode = $ssh->getExitStatus();

        $result = new GitCommandResult($exitCode === 0, $exitCode ?? 0, $output ?? '');

        return [$result, $containerTarget];
    }
}
