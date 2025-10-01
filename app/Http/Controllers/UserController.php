<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected ?AuthentikSDK $authentik;

    public function __construct(?AuthentikSDK $authentik = null)
    {
        $this->authentik = $authentik;
    }

    /**
     * Display a listing of users from both Authentik and local database
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $page = $request->get('page', 1);
            $pageSize = $request->get('page_size', 20);
            
            // Get users from Authentik
            $authentikUsers = $this->authentik ? $this->authentik->users()->all() : [];
            
            // Get users from local database
            $localUsers = User::all();
            
            // Get Portal admin group members efficiently (single API call)
            $portalAdminUserIds = [];
            if ($this->authentik) {
                try {
                    $groups = $this->authentik->groups()->all();
                    $portalAdminGroup = collect($groups)->first(function ($group) {
                        return strtolower(trim($group['name'])) === 'portal admin';
                    });
                    
                    if ($portalAdminGroup) {
                        $groupUsers = $this->authentik->groups()->getUsers($portalAdminGroup['pk']);
                        $portalAdminUserIds = collect($groupUsers)->pluck('pk')->toArray();
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch Portal admin group: ' . $e->getMessage());
                }
            }
            
            // Merge and organize the data
            $allUsers = collect($authentikUsers)->map(function ($authentikUser) use ($localUsers, $portalAdminUserIds) {
                $localUser = $localUsers->firstWhere('authentik_id', $authentikUser['pk']);
                
                // Check if user is Portal admin (from pre-fetched list)
                $isPortalAdmin = in_array($authentikUser['pk'], $portalAdminUserIds);
                
                return [
                    'id' => $authentikUser['pk'],
                    'username' => $authentikUser['username'],
                    'email' => $authentikUser['email'],
                    'name' => $authentikUser['name'],
                    'is_active' => $authentikUser['is_active'],
                    'is_superuser' => $authentikUser['is_superuser'],
                    'is_portal_admin' => $isPortalAdmin,
                    'last_login' => $authentikUser['last_login'] ? 
                        \Carbon\Carbon::parse($authentikUser['last_login'])->format('Y-m-d H:i:s') : null,
                    'date_joined' => $authentikUser['date_joined'] ? 
                        \Carbon\Carbon::parse($authentikUser['date_joined'])->format('Y-m-d H:i:s') : null,
                    'synced_locally' => !is_null($localUser),
                    'local_user' => $localUser
                ];
            });

            // Apply client-side search filtering if search term provided
            if ($search) {
                $filteredUsers = $allUsers->filter(function($user) use ($search) {
                    return stripos($user['username'], $search) !== false ||
                           stripos($user['email'], $search) !== false ||
                           stripos($user['name'], $search) !== false;
                });
                $users = $filteredUsers->values(); // Re-index collection
                $totalCount = $users->count();
                
                // For search results, we need to handle pagination manually
                $offset = ($page - 1) * $pageSize;
                $users = $users->slice($offset, $pageSize);
            } else {
                $users = $allUsers;
                $totalCount = $users->count();
                
                // Apply pagination to full results
                $offset = ($page - 1) * $pageSize;
                $users = $users->slice($offset, $pageSize);
            }

            // Add pagination info
            $pagination = [
                'current_page' => $page,
                'total' => $totalCount,
                'per_page' => $pageSize,
                'last_page' => ceil($totalCount / $pageSize),
                'has_more' => ($page * $pageSize) < $totalCount
            ];

            return view('users.index', compact('users', 'search', 'pagination'));
            
        } catch (\Exception $e) {
            return view('users.index', [
                'users' => collect([]),
                'search' => $search ?? null,
                'pagination' => null,
                'error' => 'Failed to load users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync users from Authentik
     */
    public function sync(Request $request)
    {
        Log::info('Sync request received', [
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Run the artisan command
            Log::info('Running authentik:sync command');
            $exitCode = Artisan::call('authentik:sync', ['--users' => true]);
            Log::info('Artisan command completed', ['exit_code' => $exitCode]);
            
            $output = Artisan::output();
            Log::info('Artisan output', ['output' => $output]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Users synced successfully!',
                    'output' => $output
                ]);
            }
            
            return redirect()->route('users.index')->with('success', 'Users synced successfully!');
            
        } catch (\Exception $e) {
            Log::error('Sync failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sync failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('users.index')->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        if (!$this->authentik) {
            return redirect()->route('users.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            $authentikUser = $this->authentik->users()->get($id);
            $localUser = User::where('authentik_id', $id)->first();
            
            // Try to get user's groups, but handle failure gracefully
            $groups = [];
            try {
                $groups = $this->authentik->users()->getGroups($id);
            } catch (\Exception $e) {
                Log::warning('Failed to get user groups', [
                    'user_id' => $id, 
                    'error' => $e->getMessage()
                ]);
                // Continue without groups data
            }
            
            return view('users.show', compact('authentikUser', 'localUser', 'groups'));
            
        } catch (\Exception $e) {
            Log::error('Failed to get user details', [
                'user_id' => $id, 
                'error' => $e->getMessage()
            ]);
            return redirect()->route('users.index')->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    /**
     * Show current user's profile
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $isPortalAdmin = $request->attributes->get('isPortalAdmin', false);
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's detailed information from Authentik if available
        $authentikUser = null;
        if ($this->authentik && $user->authentik_id) {
            try {
                $authentikUser = $this->authentik->users()->get($user->authentik_id);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch user details from Authentik', [
                    'user_id' => $user->id,
                    'authentik_id' => $user->authentik_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Get user's groups
        $userGroups = [];
        if ($this->authentik && $user->authentik_id) {
            try {
                $userGroups = $this->authentik->users()->getGroups($user->authentik_id);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch user groups from Authentik', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('users.profile', compact('user', 'authentikUser', 'userGroups', 'isPortalAdmin'));
    }

    /**
     * Update current user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            // Update local user record using User model
            User::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update in Authentik if possible
            if ($this->authentik && $user->authentik_id) {
                try {
                    $this->authentik->users()->update($user->authentik_id, [
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to update user in Authentik', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue anyway - local update succeeded
                }
            }

            return redirect()->route('users.profile')->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Portal admin access for a user
     */
    public function togglePortalAdmin(Request $request, $id)
    {
        if (!$this->authentik) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
            }
            return back()->with('error', 'Authentik SDK is not available.');
        }

        try {
            // Get the Portal admin group
            $groups = $this->authentik->groups()->list(['page_size' => 100]);
            $portalAdminGroup = null;
            
            foreach ($groups['results'] ?? [] as $group) {
                if (strtolower(trim($group['name'])) === 'portal admin') {
                    $portalAdminGroup = $group;
                    break;
                }
            }

            if (!$portalAdminGroup) {
                // Create Portal admin group if it doesn't exist
                $portalAdminGroup = $this->authentik->groups()->create([
                    'name' => 'Portal admin',
                    'is_superuser' => false,
                    'attributes' => [
                        'description' => 'Users with full administrative access to the Portal application',
                        'portal_role' => 'admin'
                    ]
                ]);
                
                Log::info('Created Portal admin group', ['group_id' => $portalAdminGroup['pk']]);
            }

            // Check if user is currently in Portal admin group
            $userGroups = $this->authentik->users()->getGroups($id);
            $isCurrentlyAdmin = false;
            
            foreach ($userGroups as $group) {
                if (strtolower(trim($group['name'])) === 'portal admin') {
                    $isCurrentlyAdmin = true;
                    break;
                }
            }

            if ($isCurrentlyAdmin) {
                // Remove from Portal admin group
                $this->authentik->groups()->removeUser($portalAdminGroup['pk'], $id);
                $message = 'Portal admin access removed successfully!';
                $action = 'removed';
            } else {
                // Add to Portal admin group
                $this->authentik->groups()->addUser($portalAdminGroup['pk'], $id);
                $message = 'Portal admin access granted successfully!';
                $action = 'granted';
            }

            Log::info('Portal admin access toggled', [
                'user_id' => $id,
                'action' => $action,
                'group_id' => $portalAdminGroup['pk']
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_admin' => !$isCurrentlyAdmin
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to toggle Portal admin access', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle Portal admin access: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to toggle Portal admin access: ' . $e->getMessage());
        }
    }

    /**
     * Show the user onboarding form
     */
    public function onboard()
    {
        if (!$this->authentik) {
            return redirect()->route('users.index')->with('error', 'Authentik SDK is not available.');
        }

        try {
            // Get all groups for selection
            $groupsResult = $this->authentik->groups()->list(['page_size' => 100]);
            $groups = $groupsResult['results'] ?? [];

            return view('users.onboard', compact('groups'));
        } catch (\Exception $e) {
            Log::error('Failed to load onboard form', ['error' => $e->getMessage()]);
            return redirect()->route('users.index')->with('error', 'Failed to load onboard form: ' . $e->getMessage());
        }
    }

    /**
     * Process the user onboarding
     */
    public function processOnboard(Request $request)
    {
        if (!$this->authentik) {
            return response()->json(['success' => false, 'message' => 'Authentik SDK is not available.'], 500);
        }

        try {
            // Validate the request
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'path' => 'nullable|string|max:255',
                'type' => 'required|in:internal,external,service_account',
                'groups' => 'nullable|array',
                'groups.*' => 'string',
                'generate_password' => 'boolean',
                'send_email' => 'boolean',
                'force_password_change' => 'boolean'
            ]);

            Log::info('Starting user onboarding process', [
                'username' => $validated['username'],
                'email' => $validated['email']
            ]);

            // Generate a secure password if requested (for display/record purposes)
            $password = null;
            $recoveryLink = null;
            if (($validated['generate_password'] ?? true)) {
                // Instead of setting a password directly, we'll generate a recovery link
                // This is more secure and follows Authentik's intended workflow
                $password = $this->generateSecurePassword();
            }

            // Prepare user data for Authentik
            $userData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'name' => trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')),
                'is_active' => true,
                'path' => $validated['path'] ?? 'users',
                'type' => $validated['type'] ?? 'internal'
            ];

            // Set password change requirement (this will force password setup on first login)
            if (($validated['force_password_change'] ?? true)) {
                $userData['password_change_date'] = now()->subDay()->toISOString(); // Force change
            }

            Log::info('Creating user in Authentik', ['user_data' => $userData]);

            // Create the user in Authentik
            $authentikUser = $this->authentik->users()->create($userData);

            Log::info('User created successfully in Authentik', [
                'user_id' => $authentikUser['pk'],
                'username' => $authentikUser['username']
            ]);

            // Generate recovery link for password setup instead of setting password directly
            if ($password) {
                try {
                    $recoveryResult = $this->authentik->request('POST', "/core/users/{$authentikUser['pk']}/recovery/", []);
                    
                    if (isset($recoveryResult['link'])) {
                        $recoveryLink = $recoveryResult['link'];
                        
                        Log::info('Recovery link generated for user', [
                            'user_id' => $authentikUser['pk'],
                            'link_generated' => true
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to generate recovery link for user', [
                        'user_id' => $authentikUser['pk'],
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the entire operation, but log this issue
                }
            }

            // Add user to selected groups
            if (isset($validated['groups']) && is_array($validated['groups'])) {
                foreach ($validated['groups'] as $groupId) {
                    try {
                        $this->authentik->groups()->addUser($groupId, $authentikUser['pk']);
                        Log::info('Added user to group', [
                            'user_id' => $authentikUser['pk'],
                            'group_id' => $groupId
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to add user to group', [
                            'user_id' => $authentikUser['pk'],
                            'group_id' => $groupId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Send welcome email if requested
            if (($validated['send_email'] ?? true)) {
                try {
                    $this->sendWelcomeEmail($authentikUser, $password, $recoveryLink);
                    Log::info('Welcome email sent', ['user_id' => $authentikUser['pk']]);
                } catch (\Exception $e) {
                    Log::warning('Failed to send welcome email', [
                        'user_id' => $authentikUser['pk'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Create local user record for synchronization
            try {
                User::create([
                    'authentik_id' => $authentikUser['pk'],
                    'name' => $userData['name'],
                    'email' => $authentikUser['email'],
                    'username' => $authentikUser['username']
                ]);
                Log::info('Local user record created', ['user_id' => $authentikUser['pk']]);
            } catch (\Exception $e) {
                Log::warning('Failed to create local user record', [
                    'user_id' => $authentikUser['pk'],
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User onboarded successfully',
                'username' => $authentikUser['username'],
                'password' => $password,
                'recovery_link' => $recoveryLink,
                'setup_method' => $recoveryLink ? 'recovery_link' : 'manual'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to onboard user', [
                'username' => $validated['username'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to onboard user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a secure password
     */
    private function generateSecurePassword($length = 16)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        // Ensure we have at least one of each character type
        $password .= chr(rand(97, 122)); // lowercase
        $password .= chr(rand(65, 90));  // uppercase
        $password .= chr(rand(48, 57));  // number
        $password .= '!@#$%^&*'[rand(0, 7)]; // special char
        
        // Fill the rest randomly
        for ($i = 4; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Shuffle the password to randomize the positions
        return str_shuffle($password);
    }

    /**
     * Send welcome email to the new user
     */
    private function sendWelcomeEmail($user, $password, $recoveryLink = null)
    {
        try {
            Mail::to($user['email'])->send(new WelcomeEmail($user, $password, $recoveryLink));
            Log::info('Welcome email sent successfully', [
                'user_id' => $user['pk'],
                'email' => $user['email'],
                'has_recovery_link' => !empty($recoveryLink)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user['pk'],
                'email' => $user['email'],
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw so calling code can handle it
        }
    }

    /**
     * Delete a user
     */
    public function destroy($id, Request $request)
    {
        try {
            if (!$this->authentik) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Authentik SDK not initialized'], 500);
                }
                return redirect()->route('users.index')->with('error', 'Authentik SDK not initialized');
            }

            // Get user details before deletion for logging
            $user = $this->authentik->users()->get($id);
            
            // Delete user from Authentik
            $this->authentik->users()->delete($id);
            
            // Also delete from local database if exists
            $localUser = \App\Models\User::where('authentik_id', $id)->first();
            if ($localUser) {
                $localUser->delete();
            }

            Log::info('User deleted successfully', [
                'user_id' => $id,
                'username' => $user['username'] ?? 'unknown',
                'deleted_by' => Auth::user()->username ?? 'system'
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'User deleted successfully'
                ]);
            }

            return redirect()->route('users.index')->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete user: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('users.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
