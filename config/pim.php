<?php

return [
    'enabled' => env('PIM_ENABLED', true),

    'permissions' => [
        'dashboard.view' => [
            'label' => 'View Dashboard',
            'description' => 'Access administrative dashboards and widgets.',
        ],
        'dashboard.update' => [
            'label' => 'Update Dashboard Widgets',
            'description' => 'Modify dashboard panels, tiles, or data sources.',
        ],
        'applications.view' => [
            'label' => 'View Applications',
            'description' => 'Read application metadata and assignments.',
        ],
        'applications.update' => [
            'label' => 'Update Applications',
            'description' => 'Modify application configuration or access rules.',
        ],
        'applications.delete' => [
            'label' => 'Delete Applications',
            'description' => 'Remove application entries from the portal.',
        ],
        'applications.sync' => [
            'label' => 'Sync Applications',
            'description' => 'Initiate an Authentik sync for applications.',
        ],
        'users.view' => [
            'label' => 'View Users',
            'description' => 'Read user profiles and Authentik metadata.',
        ],
        'users.update' => [
            'label' => 'Update Users',
            'description' => 'Edit user attributes and related metadata.',
        ],
        'users.delete' => [
            'label' => 'Delete Users',
            'description' => 'Remove portal users or revoke access.',
        ],
        'users.sync' => [
            'label' => 'Sync Users',
            'description' => 'Trigger Authentik user sync operations.',
        ],
        'groups.view' => [
            'label' => 'View Groups',
            'description' => 'Read group membership and hierarchy.',
        ],
        'groups.update' => [
            'label' => 'Update Groups',
            'description' => 'Modify group metadata or membership.',
        ],
        'groups.delete' => [
            'label' => 'Delete Groups',
            'description' => 'Remove portal groups.',
        ],
        'git.view' => [
            'label' => 'View Git Management',
            'description' => 'Access Git-managed server data and history.',
        ],
        'git.manage' => [
            'label' => 'Manage Git Servers',
            'description' => 'Run git commands and update managed server metadata.',
        ],
        'pim.manage' => [
            'label' => 'Manage PIM',
            'description' => 'Create groups, assign permissions, and audit activations.',
        ],
        'pim.activate' => [
            'label' => 'Activate PIM Groups',
            'description' => 'Approve or revoke PIM activations for users.',
        ],
    ],

    'default_groups' => [
        'git-management-operators' => [
            'name' => 'Git Management Operators',
            'description' => 'Temporary access to run git commands through the portal.',
            'permissions' => ['git.view', 'git.manage'],
            'min_duration_minutes' => (int) env('PIM_GIT_MIN_DURATION', 5),
            'max_duration_minutes' => (int) env('PIM_GIT_MAX_DURATION', 60),
            'default_duration_minutes' => (int) env('PIM_GIT_DEFAULT_DURATION', 15),
        ],
    ],
];
