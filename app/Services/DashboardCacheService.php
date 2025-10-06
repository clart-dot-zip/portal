<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardCacheService
{
    /**
     * Cache durations in seconds
     */
    const USER_DASHBOARD_TTL = 300; // 5 minutes
    const USER_GROUPS_TTL = 600;    // 10 minutes
    const USER_APPS_TTL = 300;      // 5 minutes
    const APPLICATIONS_TTL = 600;   // 10 minutes
    const POLICY_BINDINGS_TTL = 300; // 5 minutes

    /**
     * Clear all cache for a specific user
     */
    public static function clearUserCache(string $userId): void
    {
        $patterns = [
            "user_dashboard_{$userId}",
            "user_groups_{$userId}",
            "user_apps_{$userId}",
            "user_accessible_apps_{$userId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        Log::info('Cleared dashboard cache for user', ['user_id' => $userId]);
    }

    /**
     * Clear global application cache (when applications are modified)
     */
    public static function clearApplicationCache(): void
    {
        $patterns = [
            'all_applications',
            'all_policy_bindings',
            'policy_bindings_cache'
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Also clear all user app caches
        // Note: In a production environment, you might want to use cache tags
        // for more efficient cache invalidation
        Log::info('Cleared application cache');
    }

    /**
     * Warm up cache for a user by pre-loading common data
     */
    public static function warmUserCache(string $userId, $authentik, $accessService): void
    {
        try {
            // Pre-load user groups
            $groupsCacheKey = "user_groups_{$userId}";
            if (!Cache::has($groupsCacheKey)) {
                $userGroups = $authentik->users()->getGroups($userId);
                Cache::put($groupsCacheKey, $userGroups, self::USER_GROUPS_TTL);
            }

            // Pre-load user applications
            $appsCacheKey = "user_apps_{$userId}";
            if (!Cache::has($appsCacheKey)) {
                $personalApps = $accessService->getUserAccessibleApplications($userId);
                Cache::put($appsCacheKey, $personalApps, self::USER_APPS_TTL);
            }

            Log::info('Warmed cache for user', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::warning('Failed to warm cache for user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        // This would require additional cache store support to get real stats
        // For now, return basic information
        return [
            'cache_driver' => config('cache.default'),
            'ttl_settings' => [
                'user_dashboard' => self::USER_DASHBOARD_TTL,
                'user_groups' => self::USER_GROUPS_TTL,
                'user_apps' => self::USER_APPS_TTL,
                'applications' => self::APPLICATIONS_TTL,
                'policy_bindings' => self::POLICY_BINDINGS_TTL
            ]
        ];
    }
}