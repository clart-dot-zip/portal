<?php

namespace App\Services\Pim;

use App\Models\PimActivation;
use App\Models\User;
use App\Services\Pim\Exceptions\PimException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PimService
{
    private ServerAccessManager $serverAccessManager;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $roles;

    private bool $enabled;

    private bool $dryRun;

    public function __construct(ServerAccessManager $serverAccessManager, array $config)
    {
        $this->serverAccessManager = $serverAccessManager;
        $this->roles = Arr::get($config, 'roles', []);
        $this->enabled = (bool) Arr::get($config, 'enabled', true);
        $this->dryRun = (bool) Arr::get($config, 'dry_run', false);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isOperational(): bool
    {
        return $this->isEnabled() && $this->serverAccessManager->isConfigured();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function rolesForUser(User $user): Collection
    {
    return Collection::make($this->roles)
            ->map(function (array $role, string $key) use ($user) {
                $activeActivation = $this->getActiveActivation($user, $key);

                return [
                    'key' => $key,
                    'label' => $role['label'] ?? ucfirst($key),
                    'description' => $role['description'] ?? null,
                    'group' => $role['group'] ?? null,
                    'max_duration_minutes' => (int) ($role['max_duration_minutes'] ?? 60),
                    'default_duration_minutes' => (int) ($role['default_duration_minutes'] ?? 15),
                    'minimum_duration_minutes' => (int) ($role['minimum_duration_minutes'] ?? 5),
                    'is_active' => (bool) $activeActivation,
                    'active_activation' => $activeActivation,
                ];
            });
    }

    public function getRoleDefinition(string $roleKey): array
    {
        $role = Arr::get($this->roles, $roleKey);

        if (!$role) {
            throw new PimException("Unknown PIM role: {$roleKey}");
        }

        return $role;
    }

    public function getActiveActivation(User $user, string $roleKey): ?PimActivation
    {
        return $user->pimActivations()
            ->where('role', $roleKey)
            ->where('status', 'active')
            ->whereNull('deactivated_at')
            ->latest('activated_at')
            ->first();
    }

    public function activate(User $user, string $roleKey, int $durationMinutes, string $reason, ?User $initiator = null): PimActivation
    {
        if (!$this->isEnabled()) {
            throw new PimException('PIM is currently disabled.');
        }

        if (!$this->serverAccessManager->isConfigured()) {
            throw new PimException('PIM server is not configured.');
        }

        if (empty($user->server_username)) {
            throw new PimException('User does not have a server username configured.');
        }

        $role = $this->getRoleDefinition($roleKey);
        $group = $role['group'] ?? null;

        if (!$group) {
            throw new PimException('PIM role is missing target group configuration.');
        }

        $minimum = (int) ($role['minimum_duration_minutes'] ?? 5);
        $maximum = (int) ($role['max_duration_minutes'] ?? 60);

        if ($durationMinutes < $minimum || $durationMinutes > $maximum) {
            throw new PimException("Duration must be between {$minimum} and {$maximum} minutes.");
        }

        $activeActivation = $this->getActiveActivation($user, $roleKey);
        if ($activeActivation) {
            throw new PimException('User already has an active PIM activation for this role.');
        }

        $now = CarbonImmutable::now();
        $activation = PimActivation::create([
            'user_id' => $user->id,
            'role' => $roleKey,
            'duration_minutes' => $durationMinutes,
            'activated_at' => $now,
            'expires_at' => $now->addMinutes($durationMinutes),
            'status' => 'pending',
            'initiated_by' => $initiator ? $initiator->id : null,
            'reason' => $reason,
            'server_username_snapshot' => $user->server_username,
        ]);

        try {
            $this->serverAccessManager->addUserToGroup($user->server_username, $group);

            $activation->status = 'active';
            $activation->status_message = 'Access granted';
            $activation->save();

            Log::info('PIM activation granted', [
                'activation_id' => $activation->id,
                'user_id' => $user->id,
                'role' => $roleKey,
            ]);

            return $activation;
        } catch (\Throwable $exception) {
            $activation->status = 'failed';
            $activation->status_message = $exception->getMessage();
            $activation->save();

            throw new PimException('Failed to activate privileged role: ' . $exception->getMessage(), 0, $exception);
        }
    }

    public function deactivate(PimActivation $activation, ?string $reason = null, ?User $initiator = null): PimActivation
    {
        if (!$activation->isActive()) {
            return $activation;
        }

        $role = $this->getRoleDefinition($activation->role);
        $group = $role['group'] ?? null;

        if (!$group) {
            throw new PimException('PIM role is missing target group configuration.');
        }

        $activation->refresh();
    $user = $activation->user;
    $serverUsername = $user ? $user->server_username : $activation->server_username_snapshot;

        if (empty($serverUsername)) {
            throw new PimException('Cannot deactivate PIM activation because the server username is unavailable.');
        }

        try {
            $this->serverAccessManager->removeUserFromGroup($serverUsername, $group);
        } catch (\Throwable $exception) {
            $activation->status_message = 'Failed to remove user from group: ' . $exception->getMessage();
            $activation->save();

            throw new PimException('Failed to revoke privileged access: ' . $exception->getMessage(), 0, $exception);
        }

        $activation->status = $reason === 'expired' ? 'expired' : 'revoked';
        $activation->deactivated_at = CarbonImmutable::now();
        $activation->deactivation_reason = $reason === 'expired' ? 'Privilege window expired' : $reason;
        $activation->status_message = 'Access revoked';
        if ($initiator) {
            $activation->initiated_by = $initiator->id;
        }
        $activation->save();

        Log::info('PIM activation revoked', [
            'activation_id' => $activation->id,
            'user_id' => $activation->user_id,
            'role' => $activation->role,
            'reason' => $reason,
        ]);

        return $activation;
    }

    public function enforceExpirations(): void
    {
        PimActivation::query()
            ->where('status', 'active')
            ->where('expires_at', '<=', CarbonImmutable::now())
            ->chunkById(50, function ($activations) {
                foreach ($activations as $activation) {
                    try {
                        $this->deactivate($activation, 'expired');
                    } catch (PimException $exception) {
                        Log::error('Failed to auto-expire PIM activation', [
                            'activation_id' => $activation->id,
                            'error' => $exception->getMessage(),
                        ]);
                    }
                }
            });
    }
}
