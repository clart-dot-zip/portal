<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pterodactyl Panel API Settings
    |--------------------------------------------------------------------------
    |
    | Configure how the portal talks to your Pterodactyl panel. The application
    | API key must have permission to list servers and nodes. Optionally set
    | sensible defaults for SSH access and git behaviour when adding servers
    | into the Git Management area.
    |
    */

    'base_url' => $_ENV['PTERODACTYL_BASE_URL'] ?? $_SERVER['PTERODACTYL_BASE_URL'] ?? null,
    'api_key' => $_ENV['PTERODACTYL_API_KEY'] ?? $_SERVER['PTERODACTYL_API_KEY'] ?? null,

    /*
    |--------------------------------------------------------------------------
    | Default Git/SSH Settings
    |--------------------------------------------------------------------------
    |
    | These values act as fallbacks when new servers are onboarded. Each server
    | can override them individually, but defining defaults keeps the forms
    | concise and reduces repetition during setup.
    |
    */

    'default_remote' => $_ENV['PTERODACTYL_DEFAULT_GIT_REMOTE'] ?? $_SERVER['PTERODACTYL_DEFAULT_GIT_REMOTE'] ?? 'origin',
    'default_branch' => $_ENV['PTERODACTYL_DEFAULT_GIT_BRANCH'] ?? $_SERVER['PTERODACTYL_DEFAULT_GIT_BRANCH'] ?? 'main',
    'default_repository_path' => $_ENV['PTERODACTYL_DEFAULT_REPOSITORY_PATH'] ?? $_SERVER['PTERODACTYL_DEFAULT_REPOSITORY_PATH'] ?? '/home/container',
    'default_ssh_user' => $_ENV['PTERODACTYL_DEFAULT_SSH_USER'] ?? $_SERVER['PTERODACTYL_DEFAULT_SSH_USER'] ?? null,
    'default_ssh_port' => (int) ($_ENV['PTERODACTYL_DEFAULT_SSH_PORT'] ?? $_SERVER['PTERODACTYL_DEFAULT_SSH_PORT'] ?? 22),
    'default_ssh_key_path' => $_ENV['PTERODACTYL_DEFAULT_SSH_KEY_PATH'] ?? $_SERVER['PTERODACTYL_DEFAULT_SSH_KEY_PATH'] ?? null,

    /*
    |--------------------------------------------------------------------------
    | API Caching
    |--------------------------------------------------------------------------
    |
    | Listing servers from the application API can be relatively expensive on
    | larger panels. Cache responses for a short period to keep the UI snappy
    | while still reflecting updates made in the panel.
    |
    */

    'servers_cache_minutes' => (int) ($_ENV['PTERODACTYL_SERVERS_CACHE_MINUTES'] ?? $_SERVER['PTERODACTYL_SERVERS_CACHE_MINUTES'] ?? 5),
];
