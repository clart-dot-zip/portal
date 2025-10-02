<?php

namespace App\Services;

use App\Services\Authentik\AuthentikSDK;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApplicationAccessService
{
    protected $authentik;

    public function __construct(AuthentikSDK $authentik)
    {
        $this->authentik = $authentik;
    }

    /**
     * Check if a user has access to an application
     * Logic: If no access policies exist, everyone has access
     *        If access policies exist, only assigned users/groups have access
     */
    public function userCanAccess(string $applicationId, $userId = null): bool
    {
        try {
            $userId = $userId ?? Auth::user()?->authentik_id;
            
            if (!$userId) {
                Log::warning('Cannot check application access - no user ID available');
                return false;
            }

            // Get all policy bindings and filter manually for this application
            $bindingsResult = $this->authentik->request('GET', '/policies/bindings/', [
                'page_size' => 200
            ]);
            
            $allBindings = $bindingsResult['results'] ?? [];
            
            // Filter bindings that target this specific application
            $applicationBindings = array_filter($allBindings, function($binding) use ($applicationId) {
                return isset($binding['target']) && $binding['target'] === $applicationId;
            });
            
            // Filter to only access control bindings (group or user bindings without policies)
            $accessBindings = array_filter($applicationBindings, function($binding) {
                return ($binding['group'] || $binding['user']) && !$binding['policy'] && $binding['enabled'];
            });

            Log::info('Checking application access', [
                'application_id' => $applicationId,
                'user_id' => $userId,
                'total_bindings' => count($allBindings),
                'application_bindings' => count($applicationBindings),
                'access_bindings' => count($accessBindings)
            ]);

            // If no access policies exist, everyone has access (default allow)
            if (empty($accessBindings)) {
                Log::info('No access policies found - allowing access', [
                    'application_id' => $applicationId,
                    'user_id' => $userId
                ]);
                return true;
            }

            // If access policies exist, check if user has explicit access
            return $this->userHasExplicitAccess($applicationId, $userId, $accessBindings);

        } catch (\Exception $e) {
            Log::error('Failed to check application access', [
                'application_id' => $applicationId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if user has explicit access through group or direct user assignment
     */
    protected function userHasExplicitAccess(string $applicationId, string $userId, array $accessBindings): bool
    {
        try {
            // Check for direct user access
            foreach ($accessBindings as $binding) {
                if ($binding['user'] == $userId) {
                    Log::info('User has direct access to application', [
                        'application_id' => $applicationId,
                        'user_id' => $userId,
                        'binding_id' => $binding['pk']
                    ]);
                    return true;
                }
            }

            // Check for group access
            $userGroups = $this->authentik->users()->getGroups($userId);
            $userGroupIds = array_column($userGroups, 'pk');

            foreach ($accessBindings as $binding) {
                if ($binding['group'] && in_array($binding['group'], $userGroupIds)) {
                    Log::info('User has group access to application', [
                        'application_id' => $applicationId,
                        'user_id' => $userId,
                        'group_id' => $binding['group'],
                        'binding_id' => $binding['pk']
                    ]);
                    return true;
                }
            }

            Log::info('User does not have explicit access to application', [
                'application_id' => $applicationId,
                'user_id' => $userId,
                'user_groups' => $userGroupIds,
                'access_bindings_count' => count($accessBindings)
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to check explicit application access', [
                'application_id' => $applicationId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all applications that the current user can access
     */
    public function getUserAccessibleApplications($userId = null): array
    {
        try {
            $userId = $userId ?? Auth::user()?->authentik_id;
            
            if (!$userId) {
                return [];
            }

            // Get all applications
            $appsResult = $this->authentik->applications()->list(['page_size' => 100]);
            $allApplications = $appsResult['results'] ?? [];

            $accessibleApps = [];

            foreach ($allApplications as $app) {
                if ($this->userCanAccess($app['pk'], $userId)) {
                    $accessibleApps[] = [
                        'pk' => $app['pk'],
                        'name' => $app['name'],
                        'slug' => $app['slug'] ?? null,
                        'meta_description' => $app['meta_description'] ?? null,
                        'meta_launch_url' => $app['meta_launch_url'] ?? null,
                        'meta_icon' => $app['meta_icon'] ?? null,
                        'launch_url' => $app['meta_launch_url'] ?? '#'
                    ];
                }
            }

            Log::info('Retrieved accessible applications for user', [
                'user_id' => $userId,
                'total_applications' => count($allApplications),
                'accessible_applications' => count($accessibleApps)
            ]);

            return $accessibleApps;

        } catch (\Exception $e) {
            Log::error('Failed to get accessible applications for user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get access summary for an application (for admin view)
     */
    public function getApplicationAccessSummary(string $applicationId): array
    {
        try {
            // Get ALL policy bindings and filter manually for more reliable results
            $bindingsResult = $this->authentik->request('GET', '/policies/bindings/', [
                'page_size' => 200
            ]);
            
            $allBindings = $bindingsResult['results'] ?? [];
            
            Log::info('Filtering policy bindings for application access summary', [
                'application_id' => $applicationId,
                'total_bindings_fetched' => count($allBindings),
                'sample_targets' => array_map(function($b) { 
                    return ['target' => $b['target'] ?? 'null', 'pk' => $b['pk'] ?? 'null']; 
                }, array_slice($allBindings, 0, 5))
            ]);
            
            // Filter bindings that target this specific application
            $applicationBindings = array_filter($allBindings, function($binding) use ($applicationId) {
                return isset($binding['target']) && $binding['target'] === $applicationId;
            });
            
            // Further filter to only access control bindings (group or user bindings without policies)
            $accessBindings = array_filter($applicationBindings, function($binding) {
                return ($binding['group'] || $binding['user']) && !$binding['policy'] && $binding['enabled'];
            });

            Log::info('Application access summary calculation', [
                'application_id' => $applicationId,
                'application_bindings_count' => count($applicationBindings),
                'access_bindings_count' => count($accessBindings),
                'group_bindings' => count(array_filter($accessBindings, fn($b) => $b['group'])),
                'user_bindings' => count(array_filter($accessBindings, fn($b) => $b['user']))
            ]);

            return [
                'has_restrictions' => !empty($accessBindings),
                'access_type' => empty($accessBindings) ? 'public' : 'restricted',
                'total_bindings' => count($accessBindings),
                'group_bindings' => count(array_filter($accessBindings, fn($b) => $b['group'])),
                'user_bindings' => count(array_filter($accessBindings, fn($b) => $b['user'])),
                'message' => empty($accessBindings) 
                    ? 'This application is publicly accessible (no access restrictions)'
                    : 'This application has access restrictions - only assigned users/groups can access'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get application access summary', [
                'application_id' => $applicationId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'has_restrictions' => false,
                'access_type' => 'unknown',
                'total_bindings' => 0,
                'group_bindings' => 0,
                'user_bindings' => 0,
                'message' => 'Unable to determine access status'
            ];
        }
    }
}