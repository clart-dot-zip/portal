<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PimActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pim_group_id',
        'duration_minutes',
        'activated_at',
        'expires_at',
        'deactivated_at',
        'status',
        'initiated_by',
        'reason',
        'deactivation_reason',
        'status_message',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function pimGroup(): BelongsTo
    {
        return $this->belongsTo(PimGroup::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->deactivated_at === null;
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
}
