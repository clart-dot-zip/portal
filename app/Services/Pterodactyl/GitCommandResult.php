<?php

namespace App\Services\Pterodactyl;

class GitCommandResult
{
    public bool $successful;
    public int $exitCode;
    public string $output;
    public ?string $errorOutput;

    public function __construct(bool $successful, int $exitCode, string $output, ?string $errorOutput = null)
    {
        $this->successful = $successful;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->errorOutput = $errorOutput;
    }
}
