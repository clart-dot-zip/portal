<?php

namespace App\Services\Pim;

use App\Services\Pim\Exceptions\PimException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ServerAccessManager
{
    private ?string $host;

    private ?string $user;

    private int $port;

    private ?string $identityFile;

    /**
     * @var array<int, string>
     */
    private array $additionalOptions;

    private ?string $knownHostsFile;

    private bool $useSudo;

    private bool $dryRun;

    private string $sshBinary;

    public function __construct(array $serverConfig, bool $dryRun = false)
    {
        $this->host = Arr::get($serverConfig, 'host');
        $this->user = Arr::get($serverConfig, 'user');
        $this->port = (int) Arr::get($serverConfig, 'port', 22);
        $this->identityFile = Arr::get($serverConfig, 'identity_file');
        $additionalOptions = Arr::get($serverConfig, 'additional_options', []);
        if (is_string($additionalOptions)) {
            $additionalOptions = array_values(array_filter(preg_split('/\s+/', trim($additionalOptions)) ?: []));
        }
        $this->additionalOptions = is_array($additionalOptions) ? $additionalOptions : [];
        $this->knownHostsFile = Arr::get($serverConfig, 'known_hosts_file');
        $this->useSudo = (bool) Arr::get($serverConfig, 'use_sudo', true);
        $this->dryRun = $dryRun;
        $this->sshBinary = Arr::get($serverConfig, 'ssh_binary', 'ssh');
    }

    public function isConfigured(): bool
    {
        return !empty($this->host) && !empty($this->user);
    }

    public function userInGroup(string $username, string $group): bool
    {
        $this->assertConfigured();

        $remoteCommand = sprintf('id -nG %s', escapeshellarg($username));
        $output = $this->runRemoteCommand($remoteCommand);

        if ($output === null) {
            return false;
        }

        $groups = preg_split('/\s+/', trim($output));
        return is_array($groups) && in_array($group, $groups, true);
    }

    public function addUserToGroup(string $username, string $group): void
    {
        $this->assertConfigured();

        $command = $this->wrapPrivilegedCommand(
            sprintf('usermod -aG %s %s', escapeshellarg($group), escapeshellarg($username))
        );

        $this->runRemoteCommand($command, 'add');
    }

    public function removeUserFromGroup(string $username, string $group): void
    {
        $this->assertConfigured();

        $command = $this->wrapPrivilegedCommand(
            sprintf('gpasswd -d %s %s', escapeshellarg($username), escapeshellarg($group))
        );

        $this->runRemoteCommand($command, 'remove');
    }

    private function wrapPrivilegedCommand(string $command): string
    {
        if ($this->useSudo) {
            return 'sudo ' . $command;
        }

        return $command;
    }

    private function runRemoteCommand(string $remoteCommand, string $operation = 'exec'): ?string
    {
        $this->assertConfigured();

        if ($this->dryRun) {
            Log::info('PIM DRY RUN: Skipping remote execution', [
                'operation' => $operation,
                'command' => $remoteCommand,
            ]);
            return '';
        }

        $command = $this->buildSshCommand($remoteCommand);

        $process = new Process($command);
        $process->setTimeout(30);

        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput());

            if ($operation === 'add' && str_contains($errorOutput, 'is already a member')) {
                Log::warning('PIM add command reported existing membership', [
                    'command' => $remoteCommand,
                    'output' => $errorOutput,
                ]);
                return '';
            }

            if ($operation === 'remove' && (str_contains($errorOutput, 'is not a member') || str_contains($errorOutput, 'does not exist'))) {
                Log::warning('PIM remove command reported missing membership', [
                    'command' => $remoteCommand,
                    'output' => $errorOutput,
                ]);
                return '';
            }

            Log::error('PIM SSH command failed', [
                'operation' => $operation,
                'command' => $remoteCommand,
                'ssh_command' => $command,
                'exit_code' => $process->getExitCode(),
                'error_output' => $errorOutput,
            ]);

            throw new PimException('Failed to communicate with privileged server: ' . $errorOutput);
        }

        $output = trim($process->getOutput());

        Log::info('PIM SSH command succeeded', [
            'operation' => $operation,
            'command' => $remoteCommand,
            'output' => $output,
        ]);

        return $output;
    }

    /**
     * @return array<int, string>
     */
    private function buildSshCommand(string $remoteCommand): array
    {
        $command = [$this->sshBinary];

        if (!empty($this->identityFile)) {
            $command[] = '-i';
            $command[] = $this->identityFile;
        }

        if (!empty($this->knownHostsFile)) {
            $command[] = '-o';
            $command[] = 'UserKnownHostsFile=' . $this->knownHostsFile;
        }

        foreach ($this->additionalOptions as $option) {
            $command[] = $option;
        }

        $command[] = '-p';
        $command[] = (string) $this->port;
        $command[] = sprintf('%s@%s', $this->user, $this->host);
        $command[] = $remoteCommand;

        return $command;
    }

    private function assertConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new PimException('Privileged server connection is not configured.');
        }
    }
}
