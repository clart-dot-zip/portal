<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\DashboardCacheService;

class PreloadDashboardData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only preload for authenticated users
        $user = Auth::user();
        if ($user && $user->authentik_id) {
            // Check if we should preload data (avoid doing this on every request)
            $preloadKey = "preload_check_{$user->id}";
            
            if (!Cache::has($preloadKey)) {
                // Mark that we've checked for this user recently
                Cache::put($preloadKey, true, 60); // Check every minute at most
                
                // Dispatch background job to warm cache if needed
                // For now, just do a simple check
                $dashboardCacheKey = "user_dashboard_{$user->id}";
                if (!Cache::has($dashboardCacheKey)) {
                    // Could dispatch a job here instead of doing it synchronously
                    // For now, just log that preloading might be beneficial
                    \Illuminate\Support\Facades\Log::info('Dashboard cache miss detected for user', [
                        'user_id' => $user->id,
                        'route' => $request->route()?->getName()
                    ]);
                }
            }
        }

        return $response;
    }
}
