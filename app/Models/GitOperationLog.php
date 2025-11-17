<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GitOperationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'git_managed_server_id',
        'executed_by',
        'action',
        'command',
        'parameters',
        'status',
        'output',
        'error_output',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    /**
     * The managed server this log belongs to.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(GitManagedServer::class, 'git_managed_server_id');
    }

    /**
     * Portal user that initiated the operation.
     */
    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
