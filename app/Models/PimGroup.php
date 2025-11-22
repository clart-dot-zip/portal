<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PimGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_duration_minutes',
        'min_duration_minutes',
        'max_duration_minutes',
        'auto_approve',
    ];

    protected $casts = [
        'auto_approve' => 'boolean',
        'default_duration_minutes' => 'integer',
        'min_duration_minutes' => 'integer',
        'max_duration_minutes' => 'integer',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(PimPermission::class, 'pim_group_permission')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pim_group_user')
            ->withTimestamps();
    }

    public function activations(): HasMany
    {
        return $this->hasMany(PimActivation::class);
    }
}
