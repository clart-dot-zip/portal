<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PimPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'description',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(PimGroup::class, 'pim_group_permission')
            ->withTimestamps();
    }
}
