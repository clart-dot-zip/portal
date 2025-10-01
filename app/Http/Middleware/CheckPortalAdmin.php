<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Authentik\AuthentikSDK;

class CheckPortalAdmin
{
    protected $authentik;

    public function __construct()
    {
        try {
            $apiToken = config('services.authentik.api_token');
            if ($apiToken) {
                $this->authentik = new AuthentikSDK($apiToken);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Authentik SDK in CheckPortalAdmin middleware', [
                'error' => $e->getMessage()
            ]);
            $this->authentik = null;
        }
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $adminRequired = 'true')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has Portal admin access
        $isPortalAdmin = $this->checkPortalAdminAccess($user);
        
        // Share the admin status with the view
        view()->share('isPortalAdmin', $isPortalAdmin);
        
        // Store in request for easy access in controllers
        $request->attributes->set('isPortalAdmin', $isPortalAdmin);
        
        // If admin access is required but user is not admin, deny access
        if ($adminRequired === 'true' && !$isPortalAdmin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied. Portal admin privileges required.'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Access denied. You need Portal admin privileges to access this section.');
        }
        
        return $next($request);
    }

    /**
     * Check if the user has Portal admin access
     */
    private function checkPortalAdminAccess($user): bool
    {
        if (!$this->authentik || !$user->authentik_id) {
            Log::warning('Cannot check Portal admin access', [
                'user_id' => $user->id,
                'authentik_available' => !is_null($this->authentik),
                'authentik_id' => $user->authentik_id ?? 'missing'
            ]);
            return false;
        }

        try {
            // Get user's groups from Authentik
            $userGroups = $this->authentik->users()->getGroups($user->authentik_id);
            
            // Check if user is in "Portal admin" group
            foreach ($userGroups as $group) {
                if (strtolower(trim($group['name'])) === 'portal admin') {
                    Log::info('User has Portal admin access', [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'group_name' => $group['name']
                    ]);
                    return true;
                }
            }
            
            Log::debug('User does not have Portal admin access', [
                'user_id' => $user->id,
                'username' => $user->username,
                'groups' => array_column($userGroups, 'name')
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to check Portal admin access', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}