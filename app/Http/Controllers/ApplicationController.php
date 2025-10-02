<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    protected $authentik;

    public function __construct()
    {
              try {
                // Try to get all policy bindings and filter by target
                $bindingsResult = $this->authentik->request('GET', '/policies/bindings/', [
                    'page_size' => 100
                ]);
                $allBindings = $bindingsResult['results'] ?? [];
                
                // Filter bindings for this specific application
                $policyBindings = array_filter($allBindings, function($binding) use ($id) {
                    return isset($binding['target']) && $binding['target'] === $id;
                });
                
                Log::info('Policy bindings filtering for application edit', [
                    'application_id' => $id,
                    'total_bindings_from_api' => count($allBindings),
                    'filtered_bindings_for_app' => count($policyBindings),
                    'sample_filtered_bindings' => array_slice($policyBindings, 0, 3)
                ]);            $apiToken = config('services.authentik.api_token');
            if ($apiToken) {
                $this->authentik = new AuthentikSDK($apiToken);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Authentik SDK in ApplicationController', [
                'error' => $e->getMessage()
            ]);
            $this->authentik = null;
        }
    }

    /**
     * Display applications listing
     */
    public function index(Request $request)
    {
        if (!$this->authentik) {
            return view('applications.index', [
                'applications' => [],
                'error' => 'Authentik SDK is not available. Please check your configuration.'
            ]);
        }

        try {
            // Get pagination parameters
            $page = $request->get('page', 1);
            $pageSize = $request->get('page_size', 20);
            $search = $request->get('search');

            // Build query parameters
            $params = [
                'page' => $page,
                'page_size' => $pageSize,
                'ordering' => 'name'
            ];

            // Add search if provided
            if ($search) {
                $params['search'] = $search;
            }
            
            // Get applications from Authentik
            $result = $this->authentik->applications()->list($params);
            $applications = $result['results'] ?? [];
            
            Log::info('Applications retrieved for index', [
                'total_applications' => count($applications),
                'sample_app_keys' => !empty($applications) ? array_keys($applications[0]) : [],
                'sample_app_pk' => !empty($applications) ? $applications[0]['pk'] ?? 'no_pk' : 'no_results',
                'all_app_pks' => array_map(function($app) { return $app['pk'] ?? 'no_pk'; }, $applications)
            ]);
            
            // Add pagination info
            $pagination = [
                'current_page' => $page,
                'total' => $result['count'] ?? $result['pagination']['count'] ?? 0,
                'per_page' => $pageSize,
                'last_page' => ceil(($result['count'] ?? 0) / $pageSize),
                'has_more' => !is_null($result['next'] ?? null)
            ];

            return view('applications.index', compact('applications', 'pagination', 'search'));

        } catch (\Exception $e) {
            Log::error('Failed to get applications', ['error' => $e->getMessage()]);
            
            return view('applications.index', [
                'applications' => [],
                'error' => 'Failed to load applications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show application details
     */
    public function show($id)
    {
        if (!$this->authentik) {
            return redirect()->route('applications.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            Log::info('ApplicationController::show called', [
                'id' => $id,
                'id_type' => gettype($id)
            ]);
            
            // First check if the application exists by getting the current list
            $allAppsResult = $this->authentik->applications()->list(['page_size' => 100]);
            $allApps = $allAppsResult['results'] ?? [];
            $appExists = false;
            $targetApp = null;
            
            foreach ($allApps as $app) {
                if (($app['pk'] ?? '') === $id) {
                    $appExists = true;
                    $targetApp = $app;
                    break;
                }
            }
            
            Log::info('Application existence check', [
                'requested_id' => $id,
                'exists' => $appExists,
                'total_available_apps' => count($allApps),
                'available_app_pks' => array_map(function($app) { return $app['pk'] ?? 'no_pk'; }, array_slice($allApps, 0, 5))
            ]);
            
            if (!$appExists) {
                Log::warning('Application not found in current list', [
                    'requested_id' => $id,
                    'available_apps' => count($allApps)
                ]);
                return redirect()->route('applications.index')->with('error', 'Application not found. It may have been deleted or you may not have access to it.');
            }
            
            // Use the app data we already have instead of making another API call
            $application = $targetApp;
            
            // Get all users and groups for access management
            $allUsers = [];
            $allGroups = [];
            
            try {
                $usersResult = $this->authentik->users()->list(['page_size' => 100]);
                $allUsers = $usersResult['results'] ?? [];
            } catch (\Exception $e) {
                Log::warning('Failed to get users for application access management', ['error' => $e->getMessage()]);
            }
            
            try {
                $groupsResult = $this->authentik->groups()->list(['page_size' => 100]);
                $allGroups = $groupsResult['results'] ?? [];
            } catch (\Exception $e) {
                Log::warning('Failed to get groups for application access management', ['error' => $e->getMessage()]);
            }

            // Get current policy bindings for this application
            $policyBindings = [];
            $accessStats = [
                'direct_users' => 0,
                'groups' => 0,
                'total_policies' => 0
            ];
            
            try {
                $bindingsResult = $this->authentik->request('GET', '/policies/bindings/', [
                    'target' => $id
                ]);
                $policyBindings = $bindingsResult['results'] ?? [];
                
                // Calculate statistics server-side to avoid caching issues
                $userBindings = 0;
                $groupBindings = 0;
                $uniqueUsers = [];
                $uniqueGroups = [];
                
                foreach ($policyBindings as $binding) {
                    if (isset($binding['user']) && $binding['user'] && !isset($binding['group']) && !isset($binding['policy'])) {
                        $userBindings++;
                        $uniqueUsers[$binding['user']] = true;
                    }
                    if (isset($binding['group']) && $binding['group'] && !isset($binding['user']) && !isset($binding['policy'])) {
                        $groupBindings++;
                        $uniqueGroups[$binding['group']] = true;
                    }
                }
                
                $accessStats = [
                    'direct_users' => count($uniqueUsers),
                    'groups' => count($uniqueGroups),
                    'total_policies' => count($policyBindings)
                ];
                
                Log::info('Retrieved policy bindings for application with stats', [
                    'application_id' => $id,
                    'bindings_count' => count($policyBindings),
                    'stats' => $accessStats,
                    'sample_bindings' => array_slice($policyBindings, 0, 3)
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to get policy bindings for application', [
                    'application_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            return view('applications.show', compact('application', 'allUsers', 'allGroups', 'policyBindings', 'accessStats'));

        } catch (\Exception $e) {
            Log::error('Failed to get application details', [
                'application_id' => $id, 
                'error' => $e->getMessage()
            ]);
            return redirect()->route('applications.index')->with('error', 'Application not found: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for application
     */
    public function edit($id)
    {
        if (!$this->authentik) {
            return redirect()->route('applications.index')->with('error', 'Authentik SDK is not available.');
        }

        Log::info('Starting application edit for ID', [
            'application_id' => $id,
            'id_type' => gettype($id),
            'id_length' => strlen($id)
        ]);

        try {
            // First check if the application exists by getting the current list
            $allAppsResult = $this->authentik->applications()->list(['page_size' => 100]);
            $allApps = $allAppsResult['results'] ?? [];
            $appExists = false;
            $targetApp = null;
            
            foreach ($allApps as $app) {
                if (($app['pk'] ?? '') === $id) {
                    $appExists = true;
                    $targetApp = $app;
                    break;
                }
            }
            
            if (!$appExists) {
                Log::warning('Application not found for editing', [
                    'requested_id' => $id,
                    'available_apps' => count($allApps)
                ]);
                return redirect()->route('applications.index')->with('error', 'Application not found for editing. It may have been deleted or you may not have access to it.');
            }
            
            // Use the app data we already have
            $application = $targetApp;
            
            // Get all users and groups for access management dropdowns
            $users = [];
            $groups = [];
            $currentPolicies = [];
            
            try {
                $usersResult = $this->authentik->users()->list(['page_size' => 100]);
                $users = $usersResult['results'] ?? [];
            } catch (\Exception $e) {
                Log::warning('Failed to get users for application edit', ['error' => $e->getMessage()]);
            }
            
            try {
                $groupsResult = $this->authentik->groups()->list(['page_size' => 100]);
                $groups = $groupsResult['results'] ?? [];
            } catch (\Exception $e) {
                Log::warning('Failed to get groups for application edit', ['error' => $e->getMessage()]);
            }
            
            // Get current policy bindings for this application instead of using group field
            $currentAccess = [];
            $policyBindings = [];
            
            try {
                // Get policy bindings and filter more strictly
                $bindingsResult = $this->authentik->request('GET', '/policies/bindings/', [
                    'page_size' => 200
                ]);
                $allBindings = $bindingsResult['results'] ?? [];
                
                // Filter bindings for this specific application with strict matching
                $policyBindings = [];
                foreach ($allBindings as $binding) {
                    if (isset($binding['target']) && 
                        $binding['target'] === $id && 
                        ($binding['group'] || $binding['user']) && 
                        !$binding['policy']) {
                        $policyBindings[] = $binding;
                    }
                }
                
                Log::info('Policy bindings strict filtering for application edit', [
                    'application_id' => $id,
                    'id_type' => gettype($id),
                    'total_bindings_from_api' => count($allBindings),
                    'strict_filtered_bindings' => count($policyBindings),
                    'sample_all_targets' => array_map(function($b) { return $b['target'] ?? 'null'; }, array_slice($allBindings, 0, 10)),
                    'sample_filtered_bindings' => array_slice($policyBindings, 0, 3)
                ]);
                
                // Convert policy bindings to currentAccess format for the view
                $groupBindings = [];
                $userBindings = [];
                
                foreach ($policyBindings as $binding) {
                    if ($binding['group'] && !$binding['user'] && !$binding['policy']) {
                        // This is a group access binding - use group_id as key to avoid duplicates
                        $groupId = $binding['group'];
                        
                        if (!isset($groupBindings[$groupId])) {
                            // Find the group in our groups list
                            $groupDetails = null;
                            foreach ($groups as $group) {
                                if ($group['pk'] === $groupId) {
                                    $groupDetails = $group;
                                    break;
                                }
                            }
                            
                            $groupBindings[$groupId] = [
                                'type' => 'group',
                                'group_id' => $groupId,
                                'group_name' => $groupDetails ? $groupDetails['name'] : 'Unknown Group',
                                'binding_id' => $binding['pk'],
                                'enabled' => $binding['enabled']
                            ];
                        }
                    }
                    
                    if ($binding['user'] && !$binding['group'] && !$binding['policy']) {
                        // This is a user access binding - use user_id as key to avoid duplicates
                        $userId = $binding['user'];
                        
                        if (!isset($userBindings[$userId])) {
                            // Find the user in our users list
                            $userDetails = null;
                            foreach ($users as $user) {
                                if ($user['pk'] == $userId) {
                                    $userDetails = $user;
                                    break;
                                }
                            }
                            
                            $userBindings[$userId] = [
                                'type' => 'user',
                                'user_id' => $userId,
                                'user_name' => $userDetails ? ($userDetails['name'] ?: $userDetails['username']) : 'Unknown User',
                                'binding_id' => $binding['pk'],
                                'enabled' => $binding['enabled']
                            ];
                        }
                    }
                }
                
                // Merge deduplicated bindings into currentAccess
                $currentAccess = array_merge(array_values($groupBindings), array_values($userBindings));
                
                Log::info('Retrieved policy bindings for application edit', [
                    'application_id' => $id,
                    'bindings_count' => count($policyBindings),
                    'group_access_count' => count(array_filter($currentAccess, fn($a) => $a['type'] === 'group')),
                    'user_access_count' => count(array_filter($currentAccess, fn($a) => $a['type'] === 'user')),
                    'raw_bindings' => $policyBindings,
                    'processed_access' => $currentAccess
                ]);
                
            } catch (\Exception $e) {
                Log::warning('Failed to get policy bindings for application edit', [
                    'application_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            return view('applications.edit', compact('application', 'users', 'groups', 'currentAccess', 'policyBindings'));

        } catch (\Exception $e) {
            Log::error('Failed to get application for editing', [
                'application_id' => $id, 
                'error' => $e->getMessage()
            ]);
            return redirect()->route('applications.index')->with('error', 'Application not found: ' . $e->getMessage());
        }
    }

    /**
     * Update application
     */
    public function update(Request $request, $id)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        try {
            $data = $request->only(['name', 'slug', 'meta_description', 'meta_launch_url', 'group']);
            
            // Handle group assignment (can be null to remove group)
            if ($request->has('group')) {
                Log::info('Updating application group assignment', [
                    'application_id' => $id,
                    'new_group' => $request->group
                ]);
            }
            
            // Use direct API call instead of manager to avoid 404 issues
            $application = $this->authentik->request('PATCH', "/core/applications/{$id}/", $data);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application updated successfully',
                    'application' => $application
                ]);
            }

            return redirect()->route('applications.show', $id)->with('success', 'Application updated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to update application', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update application: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('applications.edit', $id)->with('error', 'Failed to update application: ' . $e->getMessage());
        }
    }

    /**
     * Assign group access to application via policy bindings
     */
    public function assignGroupAccess(Request $request, $id)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        $request->validate([
            'group_id' => 'required|string'
        ]);

        try {
            $groupId = $request->input('group_id');
            
            Log::info("Starting group assignment via policy binding", [
                'application_id' => $id,
                'group_id' => $groupId
            ]);

            // First verify the application exists by listing all apps
            $allAppsResult = $this->authentik->applications()->list(['page_size' => 100]);
            $allApps = $allAppsResult['results'] ?? [];
            $application = null;
            
            foreach ($allApps as $app) {
                if (($app['pk'] ?? '') === $id) {
                    $application = $app;
                    break;
                }
            }

            if (!$application) {
                Log::error('Application not found in current list for group assignment', [
                    'requested_id' => $id,
                    'available_apps' => count($allApps)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Get existing policy bindings for this application
            $existingBindings = $this->authentik->request('GET', '/policies/bindings/', [
                'target' => $id
            ]);

            Log::info("Found existing policy bindings", [
                'count' => count($existingBindings['results']),
                'bindings' => array_map(function($b) { 
                    return ['group' => $b['group'], 'user' => $b['user'], 'policy' => $b['policy']]; 
                }, $existingBindings['results'])
            ]);

            // Check if group is already bound to this application
            foreach ($existingBindings['results'] as $binding) {
                if ($binding['group'] === $groupId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Group already has access to this application'
                    ]);
                }
            }

            // Create a policy binding for the group and application
            // Note: We'll create a binding without a specific policy, which typically allows access
            $bindingData = [
                'target' => $id,
                'group' => $groupId,
                'enabled' => true,
                'order' => 0,
                'negate' => false
            ];

            Log::info("Creating policy binding", ['data' => $bindingData]);

            $result = $this->authentik->request('POST', '/policies/bindings/', $bindingData);

            Log::info("Policy binding created successfully", ['result' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Group access assigned successfully via policy binding',
                'binding' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to assign group access via policy binding", [
                'application_id' => $id,
                'group_id' => $request->input('group_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign group access: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign user access to application
     */
    public function assignUserAccess(Request $request, $id)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        $request->validate([
            'user_id' => 'required|string'
        ]);

        try {
            // Authentik doesn't support direct user-to-application assignment
            // Users get access through groups. We should inform the user of this.
            
            Log::info('User access assignment attempted', [
                'application_id' => $id,
                'user_id' => $request->user_id,
                'note' => 'Authentik requires users to be assigned through groups'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Direct user access assignment is not supported by Authentik. Please add the user to a group that has access to this application instead.',
                'suggestion' => 'Use group assignment and then add users to that group.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to assign user access to application', [
                'application_id' => $id,
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign user access: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add user access to application
     */
    public function addUserAccess(Request $request, $id)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        $request->validate([
            'user_id' => 'required|integer'
        ]);

        try {
            // Create a policy binding for the user
            $bindingData = [
                'target' => $id,
                'user' => $request->user_id,
                'enabled' => true,
                'order' => 0
            ];

            $binding = $this->authentik->request('POST', '/policies/bindings/', $bindingData);

            Log::info('User access added to application', [
                'application_id' => $id,
                'user_id' => $request->user_id,
                'binding_id' => $binding['pk'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User access added successfully',
                'binding' => $binding
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add user access to application', [
                'application_id' => $id,
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add user access: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove access from application
     */
    public function removeAccess(Request $request, $id)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        $request->validate([
            'policy_id' => 'required|string'
        ]);

        try {
            $this->authentik->request('DELETE', "/policies/bindings/{$request->policy_id}/");

            Log::info('Access removed from application', [
                'application_id' => $id,
                'policy_id' => $request->policy_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Access removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove access from application', [
                'application_id' => $id,
                'policy_id' => $request->policy_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove access: ' . $e->getMessage()
            ], 500);
        }
    }
}