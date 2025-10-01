<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class GroupController extends Controller
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
            Log::error('Failed to initialize Authentik SDK in GroupController', [
                'error' => $e->getMessage()
            ]);
            $this->authentik = null;
        }
    }

    /**
     * Display groups listing
     */
    public function index(Request $request)
    {
        if (!$this->authentik) {
            return view('groups.index', [
                'groups' => [],
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

            // Note: Authentik API doesn't seem to support search on groups endpoint
            // We'll get all groups and filter client-side
            
            // Get groups from Authentik
            $result = $this->authentik->groups()->list($params);
            $allGroups = $result['results'] ?? [];
            
            // Apply client-side search filtering if search term provided
            if ($search) {
                $filteredGroups = array_filter($allGroups, function($group) use ($search) {
                    return stripos($group['name'], $search) !== false;
                });
                $groups = array_values($filteredGroups); // Re-index array
                $totalCount = count($groups);
                
                // For search results, we need to handle pagination manually
                $offset = ($page - 1) * $pageSize;
                $groups = array_slice($groups, $offset, $pageSize);
            } else {
                $groups = $allGroups;
                // Get total count from API response
                $totalCount = $result['count'] ?? $result['pagination']['count'] ?? 0;
            }

            // Add pagination info - handle both API and client-side filtering
            $pagination = [
                'current_page' => $page,
                'total' => $totalCount,
                'per_page' => $pageSize,
                'last_page' => ceil($totalCount / $pageSize),
                'has_more' => ($page * $pageSize) < $totalCount
            ];

            return view('groups.index', compact('groups', 'pagination', 'search'));

        } catch (\Exception $e) {
            Log::error('Failed to get groups', ['error' => $e->getMessage()]);
            
            return view('groups.index', [
                'groups' => [],
                'error' => 'Failed to load groups: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show group details
     */
    public function show($id)
    {
        if (!$this->authentik) {
            return redirect()->route('groups.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            $group = $this->authentik->groups()->get($id);
            
            // Get group members using the working API endpoint
            $members = [];
            try {
                $members = $this->authentik->groups()->getUsers($id);
            } catch (\Exception $e) {
                Log::warning('Failed to get group members', [
                    'group_id' => $id, 
                    'error' => $e->getMessage()
                ]);
            }

            return view('groups.show', compact('group', 'members'));

        } catch (\Exception $e) {
            Log::error('Failed to get group details', [
                'group_id' => $id, 
                'error' => $e->getMessage()
            ]);
            return redirect()->route('groups.index')->with('error', 'Group not found: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for group
     */
    public function edit($id)
    {
        if (!$this->authentik) {
            return redirect()->route('groups.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            $group = $this->authentik->groups()->get($id);
            
            // Get all available users for adding to group
            $availableUsers = [];
            try {
                $usersResult = $this->authentik->users()->list(['page_size' => 100]);
                $availableUsers = $usersResult['results'] ?? [];
            } catch (\Exception $e) {
                Log::warning('Failed to get users for group editing', ['error' => $e->getMessage()]);
            }

            // Get current group members using the working API endpoint
            $currentMembers = [];
            try {
                $currentMembers = $this->authentik->groups()->getUsers($id);
            } catch (\Exception $e) {
                Log::warning('Failed to get current group members', [
                    'group_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            return view('groups.edit', compact('group', 'availableUsers', 'currentMembers'));

        } catch (\Exception $e) {
            Log::error('Failed to get group for editing', [
                'group_id' => $id, 
                'error' => $e->getMessage()
            ]);
            return redirect()->route('groups.index')->with('error', 'Group not found: ' . $e->getMessage());
        }
    }

    /**
     * Update group properties
     */
    public function update(Request $request, $id)
    {
        if (!$this->authentik) {
            return redirect()->route('groups.index')->with('error', 'Authentik SDK is not available.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'is_superuser' => 'boolean',
            'parent' => 'nullable|string',
            'attributes' => 'nullable|json'
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'is_superuser' => $request->boolean('is_superuser', false)
            ];

            if ($request->filled('parent')) {
                $updateData['parent'] = $request->parent;
            }

            if ($request->filled('attributes')) {
                $decodedAttributes = json_decode($request->input('attributes'), true);
                
                // Only include attributes if it's not empty or null
                // Send as object if empty, or don't send at all
                if (!empty($decodedAttributes)) {
                    $updateData['attributes'] = $decodedAttributes;
                } else {
                    // For empty attributes, send as empty object instead of empty array
                    $updateData['attributes'] = (object)[];
                }
            }

            $this->authentik->groups()->update($id, $updateData);

            return redirect()->route('groups.show', $id)->with('success', 'Group updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update group', [
                'group_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Failed to update group: ' . $e->getMessage());
        }
    }

    /**
     * Add user to group
     */
    public function addUser(Request $request, $id)
    {
        if (!$this->authentik) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
            }
            return back()->with('error', 'Authentik SDK is not available.');
        }

        $request->validate([
            'user_id' => 'required|string'
        ]);

        try {
            $this->authentik->groups()->addUser($id, $request->user_id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User added to group successfully!'
                ]);
            }

            return back()->with('success', 'User added to group successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to add user to group', [
                'group_id' => $id,
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add user to group: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to add user to group: ' . $e->getMessage());
        }
    }

    /**
     * Remove user from group
     */
    public function removeUser(Request $request, $id, $userId)
    {
        if (!$this->authentik) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
            }
            return back()->with('error', 'Authentik SDK is not available.');
        }

        try {
            $this->authentik->groups()->removeUser($id, $userId);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User removed from group successfully!'
                ]);
            }

            return back()->with('success', 'User removed from group successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to remove user from group', [
                'group_id' => $id,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove user from group: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to remove user from group: ' . $e->getMessage());
        }
    }

    /**
     * Sync groups from Authentik
     */
    public function sync(Request $request)
    {
        if (!$this->authentik) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentik SDK is not available.'
                ], 500);
            }
            
            return redirect()->route('groups.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            // Run the sync command
            $exitCode = Artisan::call('authentik:sync', ['--groups' => true]);
            $output = Artisan::output();

            Log::info('Groups sync completed', ['output' => $output, 'exit_code' => $exitCode]);

            // Check if the command was successful
            if ($exitCode !== 0) {
                throw new \Exception('Sync command failed with exit code: ' . $exitCode);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Groups synced successfully!'
                ], 200);
            }
            
            return redirect()->route('groups.index')->with('success', 'Groups synced successfully!');
            
        } catch (\Exception $e) {
            Log::error('Groups sync failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sync failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('groups.index')->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}