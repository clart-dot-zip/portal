<?php

namespace App\Services\Pim;

use App\Models\PimActivation;
use App\Models\PimGroup;
use App\Models\PimPermission;
use App\Models\User;
use App\Services\Pim\Exceptions\PimException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PimService
{
    private bool $enabled;

    private ?bool $operationalCache = null;

    public function __construct(array $config)
    {
        $this->enabled = (bool) Arr::get($config, 'enabled', true);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isOperational(): bool
    {
        if ($this->operationalCache !== null) {
            return $this->operationalCache;
        }

        if (!$this->isEnabled()) {
            return $this->operationalCache = false;
        }

        return $this->operationalCache = PimGroup::query()->exists() && PimPermission::query()->exists();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function groupsForUser(User $user): Collection
    {
        $groups = $user->pimGroups()->with('permissions')->orderBy('name')->get();

        $activations = $user->pimActivations()
            ->where('status', 'active')
            ->whereNull('deactivated_at')
            ->get()
            ->keyBy('pim_group_id');

        return $groups->map(function (PimGroup $group) use ($activations) {
            $activeActivation = $activations->get($group->id);

            return [
                'id' => $group->id,
                'label' => $group->name,
                'description' => $group->description,
                'permissions' => $group->permissions,
                'max_duration_minutes' => $group->max_duration_minutes,
                'default_duration_minutes' => $group->default_duration_minutes,
                'minimum_duration_minutes' => $group->min_duration_minutes,
                'is_active' => (bool) $activeActivation,
                'active_activation' => $activeActivation,
                'group' => $group,
            ];
        });
    }

    public function getGroupById(int $groupId): PimGroup
    {
        $group = PimGroup::with('permissions')->find($groupId);

        if (!$group) {
            throw new PimException('Unknown PIM group.');
        }

        return $group;
    }

    public function getActiveActivation(User $user, PimGroup $group): ?PimActivation
    {
        return $user->pimActivations()
            ->where('pim_group_id', $group->id)
            ->where('status', 'active')
            ->whereNull('deactivated_at')
            ->latest('activated_at')
            ->first();
    }

    public function activate(User $user, PimGroup $group, int $durationMinutes, string $reason, ?User $initiator = null): PimActivation
    {
        if (!$this->isEnabled()) {
            throw new PimException('PIM is currently disabled.');
        }

        $assignmentExists = $user->pimGroups()->where('pim_group_id', $group->id)->exists();

        if (!$assignmentExists) {
            throw new PimException('User is not assigned to this PIM group.');
        }

        $minimum = (int) $group->min_duration_minutes;
        $maximum = (int) $group->max_duration_minutes;

        if ($durationMinutes < $minimum || $durationMinutes > $maximum) {
            throw new PimException("Duration must be between {$minimum} and {$maximum} minutes.");
        }

        $activeActivation = $this->getActiveActivation($user, $group);
        if ($activeActivation) {
            throw new PimException('User already has an active PIM activation for this group.');
        }

        $now = CarbonImmutable::now();
        $activation = PimActivation::create([
            'user_id' => $user->id,
            'pim_group_id' => $group->id,
            'duration_minutes' => $durationMinutes,
            'activated_at' => $now,
            'expires_at' => $now->addMinutes($durationMinutes),
            'status' => 'active',
            'initiated_by' => $initiator ? $initiator->id : null,
            'reason' => $reason,
            'status_message' => 'Access granted',
        ]);

        Log::info('PIM activation granted', [
            'activation_id' => $activation->id,
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        return $activation;
    }

    public function deactivate(PimActivation $activation, ?string $reason = null, ?User $initiator = null): PimActivation
    {
        if (!$activation->isActive()) {
            return $activation;
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
            'group_id' => $activation->pim_group_id,
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
