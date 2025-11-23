<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use App\Models\PimActivation;
use App\Models\PimGroup;
use App\Models\PimPermission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected ?Collection $cachedActivePimPermissions = null;

    protected ?bool $cachedHasPimGroups = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'authentik_id',
        'username',
        'server_username',
        'is_active',
        'last_login',
        'authentik_attributes',
        'avatar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'authentik_attributes' => 'array',
        'password' => 'hashed',
    ];

    public function pimActivations(): HasMany
    {
        return $this->hasMany(PimActivation::class);
    }

    public function pimGroups(): BelongsToMany
    {
        return $this->belongsToMany(PimGroup::class, 'pim_group_user')->withTimestamps();
    }

    public function activePimPermissions(): Collection
    {
        if ($this->cachedActivePimPermissions !== null) {
            return $this->cachedActivePimPermissions;
        }

        return $this->cachedActivePimPermissions = PimPermission::query()
            ->whereHas('groups.activations', function ($query) {
                $query->where('user_id', $this->id)
                    ->where('status', 'active')
                    ->whereNull('deactivated_at');
            })
            ->pluck('key')
            ->unique()
            ->values();
    }

    public function hasActivePimPermission(string $permissionKey): bool
    {
        return $this->activePimPermissions()->contains($permissionKey);
    }

    public function hasAssignedPimGroups(): bool
    {
        if ($this->cachedHasPimGroups !== null) {
            return $this->cachedHasPimGroups;
        }

        return $this->cachedHasPimGroups = $this->pimGroups()->exists();
    }
}
