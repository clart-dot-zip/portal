<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GitManagedServer extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     *
     * Keeping the list explicit makes it easier to audit which values the
     * admin UI can change while protecting operational fields.
     */
    protected $fillable = [
        'pterodactyl_server_id',
        'pterodactyl_server_uuid',
        'pterodactyl_server_identifier',
        'name',
        'description',
        'pterodactyl_node_id',
        'pterodactyl_node_name',
        'container_name',
        'repository_path',
        'repository_url',
        'default_branch',
        'remote_name',
        'ssh_host',
        'ssh_port',
        'ssh_username',
        'ssh_private_key_path',
        'created_by',
    ];

    protected $casts = [
        'pterodactyl_server_id' => 'integer',
        'pterodactyl_node_id' => 'integer',
        'ssh_port' => 'integer',
    ];

    /**
     * Operation logs associated with the managed server.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(GitOperationLog::class);
    }

    /**
     * User that initially configured the server inside the portal.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Convenience accessor returning the docker target name.
     */
    public function getDockerExecTargetAttribute(): string
    {
    $containerName = $this->attributes['container_name'] ?? null;
        if (!empty($containerName)) {
            return $containerName;
        }

    $serverUuid = $this->attributes['pterodactyl_server_uuid'] ?? null;
        if (!empty($serverUuid)) {
            return $serverUuid;
        }

    $identifier = $this->attributes['pterodactyl_server_identifier'] ?? null;
        if (!empty($identifier)) {
            return 'pterodactyl_' . $identifier;
        }

        return 'pterodactyl_container';
    }
}
