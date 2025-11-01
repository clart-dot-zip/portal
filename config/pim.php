<?php

return [
    'enabled' => env('PIM_ENABLED', true),

    'dry_run' => env('PIM_DRY_RUN', false),

    'server' => [
        'host' => env('PIM_SERVER_HOST'),
        'port' => env('PIM_SERVER_PORT', 22),
        'user' => env('PIM_SERVER_USER'),
        'identity_file' => env('PIM_SERVER_IDENTITY_FILE'),
        'ssh_binary' => env('PIM_SSH_BINARY', 'ssh'),
        'additional_options' => env('PIM_SSH_OPTIONS'),
        'known_hosts_file' => env('PIM_KNOWN_HOSTS_FILE'),
        'use_sudo' => env('PIM_USE_SUDO', true),
    ],

    'roles' => [
        'root' => [
            'label' => 'Root',
            'description' => 'Temporary root access on the dedicated server.',
            'group' => env('PIM_ROOT_GROUP', 'root'),
            'max_duration_minutes' => env('PIM_ROOT_MAX_DURATION', 60),
            'default_duration_minutes' => env('PIM_ROOT_DEFAULT_DURATION', 15),
            'minimum_duration_minutes' => env('PIM_ROOT_MIN_DURATION', 5),
        ],
    ],
];
