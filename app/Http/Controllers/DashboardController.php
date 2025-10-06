<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use App\Services\ApplicationAccessService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $authentik;
    protected $accessService;

    public function __construct()
    {
        try {
            $apiToken = config('services.authentik.api_token');
            if ($apiToken) {
                $this->authentik = new AuthentikSDK($apiToken);
                $this->accessService = app(ApplicationAccessService::class);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize services in DashboardController', [
                'error' => $e->getMessage()
            ]);
            $this->authentik = null;
            $this->accessService = null;
        }
    }

    /**
     * Display the user dashboard (optimized with caching)
     */
    public function index(Request $request)
    {
        $isPortalAdmin = $request->attributes->get('isPortalAdmin', false);
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Cache key for this user's dashboard data
        $dashboardCacheKey = "user_dashboard_{$user->id}";
        
        // Try to get cached dashboard data (cache for 5 minutes)
        $cachedData = Cache::get($dashboardCacheKey);
        if ($cachedData !== null) {
            Log::info('Retrieved dashboard data from cache', [
                'user_id' => $user->id,
                'cache_key' => $dashboardCacheKey
            ]);
            
            return view('dashboard.user', array_merge($cachedData, ['isPortalAdmin' => $isPortalAdmin]));
        }

        // Basic stats available to all users
        $userStats = [
            'profile_complete' => true,
            'groups_count' => 0,
            'last_login' => $user->last_login,
            'account_created' => $user->created_at,
            'groups' => []
        ];

        // Get user's groups if Authentik is available (with caching)
        if ($this->authentik && $user->authentik_id) {
            $userStats['groups'] = $this->getUserGroupsCached($user->authentik_id);
            $userStats['groups_count'] = count($userStats['groups']);
        }

        // Get user's accessible applications (with caching)
        $personalApps = [];
        if ($this->accessService && $user->authentik_id) {
            $personalApps = $this->getUserApplicationsCached($user->authentik_id);
        }

        // Prepare data for caching
        $dashboardData = [
            'userStats' => $userStats,
            'personalApps' => $personalApps
        ];

        // Cache the results for 5 minutes
        Cache::put($dashboardCacheKey, $dashboardData, 300);

        Log::info('Generated and cached dashboard data', [
            'user_id' => $user->id,
            'applications_count' => count($personalApps),
            'groups_count' => $userStats['groups_count']
        ]);

        return view('dashboard.user', array_merge($dashboardData, ['isPortalAdmin' => $isPortalAdmin]));
    }

    /**
     * Get user groups with caching
     */
    private function getUserGroupsCached($userId): array
    {
        $cacheKey = "user_groups_{$userId}";
        
        return Cache::remember($cacheKey, 600, function () use ($userId) { // Cache for 10 minutes
            try {
                if (!$this->authentik) {
                    return [];
                }
                
                $userGroups = $this->authentik->users()->getGroups($userId);
                
                Log::info('Fetched user groups from API', [
                    'user_id' => $userId,
                    'groups_count' => count($userGroups)
                ]);
                
                return $userGroups;
            } catch (\Exception $e) {
                Log::warning('Failed to fetch user groups for dashboard', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Get user applications with caching and optimization
     */
    private function getUserApplicationsCached($userId): array
    {
        $cacheKey = "user_apps_{$userId}";
        
        return Cache::remember($cacheKey, 300, function () use ($userId) { // Cache for 5 minutes
            try {
                if (!$this->accessService) {
                    return [];
                }
                
                // Use optimized access service method
                $personalApps = $this->accessService->getUserAccessibleApplications($userId);
                
                Log::info('Fetched user applications from API', [
                    'user_id' => $userId,
                    'applications_count' => count($personalApps)
                ]);
                
                return $personalApps;
            } catch (\Exception $e) {
                Log::warning('Failed to fetch accessible applications for user dashboard', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Display the admin dashboard with full system statistics
     */
    public function adminDashboard(Request $request)
    {
        $isPortalAdmin = $request->attributes->get('isPortalAdmin', false);
        
        if (!$isPortalAdmin) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        
        // Full system stats for admins
        $stats = [
            'users' => [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'recent_logins' => 0
            ],
            'groups' => [
                'total' => 0,
                'superuser_groups' => 0,
                'empty_groups' => 0
            ],
            'applications' => [
                'total' => 0
            ],
            'system' => [
                'authentik_status' => 'unknown',
                'api_response_time' => 0,
                'last_sync' => null
            ]
        ];

        $chartData = [
            'user_activity' => [],
            'group_membership' => [],
            'login_activity' => []
        ];

        if ($this->authentik) {
            try {
                // Measure API response time
                $startTime = microtime(true);
                
                // Get user statistics
                $usersResult = $this->authentik->users()->list(['page_size' => 100]);
                $users = $usersResult['results'] ?? [];
                
                $endTime = microtime(true);
                $stats['system']['api_response_time'] = round(($endTime - $startTime) * 1000, 2); // in milliseconds
                $stats['system']['authentik_status'] = 'connected';
                
                $stats['users']['total'] = count($users);
                $recentLoginThreshold = Carbon::now()->subDays(7);
                
                foreach ($users as $user) {
                    if ($user['is_active'] ?? true) {
                        $stats['users']['active']++;
                    } else {
                        $stats['users']['inactive']++;
                    }
                    
                    // Check for recent logins
                    if (isset($user['last_login']) && $user['last_login']) {
                        $lastLogin = Carbon::parse($user['last_login']);
                        if ($lastLogin->greaterThan($recentLoginThreshold)) {
                            $stats['users']['recent_logins']++;
                        }
                    }
                }

                // Get group statistics
                $groupsResult = $this->authentik->groups()->list(['page_size' => 100]);
                $groups = $groupsResult['results'] ?? [];
                
                $stats['groups']['total'] = count($groups);
                $groupMembershipData = [];
                
                foreach ($groups as $group) {
                    if ($group['is_superuser'] ?? false) {
                        $stats['groups']['superuser_groups']++;
                    }
                    
                    $memberCount = count($group['users'] ?? []);
                    if ($memberCount === 0) {
                        $stats['groups']['empty_groups']++;
                    }
                    
                    // Data for group membership chart
                    if ($memberCount > 0) {
                        $groupMembershipData[] = [
                            'name' => $group['name'],
                            'members' => $memberCount
                        ];
                    }
                }

                // Sort groups by member count for chart
                usort($groupMembershipData, function($a, $b) {
                    return $b['members'] - $a['members'];
                });
                
                // Take top 10 groups for chart
                $chartData['group_membership'] = array_slice($groupMembershipData, 0, 10);

                // Get applications count (if endpoint exists)
                try {
                    $appsResult = $this->authentik->request('GET', '/core/applications/');
                    $stats['applications']['total'] = count($appsResult['results'] ?? []);
                } catch (\Exception $e) {
                    // Applications endpoint might not be available
                    Log::info('Applications endpoint not available for dashboard stats');
                }

                // Generate user activity chart data (active vs inactive)
                $chartData['user_activity'] = [
                    ['status' => 'Active', 'count' => $stats['users']['active']],
                    ['status' => 'Inactive', 'count' => $stats['users']['inactive']]
                ];

                // Generate login activity data (mock data for recent activity)
                $chartData['login_activity'] = [
                    ['period' => 'Last 24h', 'logins' => $stats['users']['recent_logins']],
                    ['period' => 'This Week', 'logins' => $stats['users']['recent_logins']],
                    ['period' => 'This Month', 'logins' => $stats['users']['total']]
                ];

            } catch (\Exception $e) {
                $stats['system']['authentik_status'] = 'error';
                Log::error('Failed to gather dashboard statistics', [
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $stats['system']['authentik_status'] = 'disconnected';
        }

        return view('dashboard.admin', compact('stats', 'chartData', 'isPortalAdmin'));
    }

    /**
     * Legacy method - now redirects to appropriate dashboard
     */
    public function legacyIndex()
    {
        $stats = [
            'users' => [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'recent_logins' => 0
            ],
            'groups' => [
                'total' => 0,
                'superuser_groups' => 0,
                'empty_groups' => 0
            ],
            'applications' => [
                'total' => 0
            ],
            'system' => [
                'authentik_status' => 'unknown',
                'api_response_time' => 0,
                'last_sync' => null
            ]
        ];

        $chartData = [
            'user_activity' => [],
            'group_membership' => [],
            'login_activity' => []
        ];

        if ($this->authentik) {
            try {
                // Measure API response time
                $startTime = microtime(true);
                
                // Get user statistics
                $usersResult = $this->authentik->users()->list(['page_size' => 100]);
                $users = $usersResult['results'] ?? [];
                
                $endTime = microtime(true);
                $stats['system']['api_response_time'] = round(($endTime - $startTime) * 1000, 2); // in milliseconds
                $stats['system']['authentik_status'] = 'connected';
                
                $stats['users']['total'] = count($users);
                $recentLoginThreshold = Carbon::now()->subDays(7);
                
                foreach ($users as $user) {
                    if ($user['is_active'] ?? true) {
                        $stats['users']['active']++;
                    } else {
                        $stats['users']['inactive']++;
                    }
                    
                    // Check for recent logins
                    if (isset($user['last_login']) && $user['last_login']) {
                        $lastLogin = Carbon::parse($user['last_login']);
                        if ($lastLogin->greaterThan($recentLoginThreshold)) {
                            $stats['users']['recent_logins']++;
                        }
                    }
                }

                // Get group statistics
                $groupsResult = $this->authentik->groups()->list(['page_size' => 100]);
                $groups = $groupsResult['results'] ?? [];
                
                $stats['groups']['total'] = count($groups);
                $groupMembershipData = [];
                
                foreach ($groups as $group) {
                    if ($group['is_superuser'] ?? false) {
                        $stats['groups']['superuser_groups']++;
                    }
                    
                    $memberCount = count($group['users'] ?? []);
                    if ($memberCount === 0) {
                        $stats['groups']['empty_groups']++;
                    }
                    
                    // Data for group membership chart
                    if ($memberCount > 0) {
                        $groupMembershipData[] = [
                            'name' => $group['name'],
                            'members' => $memberCount
                        ];
                    }
                }

                // Sort groups by member count for chart
                usort($groupMembershipData, function($a, $b) {
                    return $b['members'] - $a['members'];
                });
                
                // Take top 10 groups for chart
                $chartData['group_membership'] = array_slice($groupMembershipData, 0, 10);

                // Get applications count (if endpoint exists)
                try {
                    $appsResult = $this->authentik->request('GET', '/core/applications/');
                    $stats['applications']['total'] = count($appsResult['results'] ?? []);
                } catch (\Exception $e) {
                    // Applications endpoint might not be available
                    Log::info('Applications endpoint not available for dashboard stats');
                }

                // Generate user activity chart data (active vs inactive)
                $chartData['user_activity'] = [
                    ['status' => 'Active', 'count' => $stats['users']['active']],
                    ['status' => 'Inactive', 'count' => $stats['users']['inactive']]
                ];

                // Generate login activity data (mock data for recent activity)
                $chartData['login_activity'] = [
                    ['period' => 'Last 24h', 'logins' => $stats['users']['recent_logins']],
                    ['period' => 'This Week', 'logins' => $stats['users']['recent_logins']],
                    ['period' => 'This Month', 'logins' => $stats['users']['total']]
                ];

            } catch (\Exception $e) {
                $stats['system']['authentik_status'] = 'error';
                Log::error('Failed to gather dashboard statistics', [
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $stats['system']['authentik_status'] = 'disconnected';
        }

        return view('dashboard.admin', compact('stats', 'chartData', 'isPortalAdmin'));
    }
}