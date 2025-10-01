<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Artisan;
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
            
            // Merge and organize the data
            $allUsers = collect($authentikUsers)->map(function ($authentikUser) use ($localUsers) {
                $localUser = $localUsers->firstWhere('authentik_id', $authentikUser['pk']);
                
                return [
                    'id' => $authentikUser['pk'],
                    'username' => $authentikUser['username'],
                    'email' => $authentikUser['email'],
                    'name' => $authentikUser['name'],
                    'is_active' => $authentikUser['is_active'],
                    'is_superuser' => $authentikUser['is_superuser'],
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

            // Generate a secure password if requested
            $password = null;
            if (($validated['generate_password'] ?? true)) {
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

            // Add password if generated
            if ($password) {
                $userData['password'] = $password;
            }

            // Set password change requirement
            if (($validated['force_password_change'] ?? true)) {
                $userData['password_change_date'] = now()->subDay()->toISOString(); // Force change
            }

            Log::info('Creating user in Authentik', ['user_data' => array_merge($userData, ['password' => $password ? '[GENERATED]' : '[NONE]'])]);

            // Create the user in Authentik
            $authentikUser = $this->authentik->users()->create($userData);

            Log::info('User created successfully in Authentik', [
                'user_id' => $authentikUser['pk'],
                'username' => $authentikUser['username']
            ]);

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
                    $this->sendWelcomeEmail($authentikUser, $password);
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
                'password' => $password
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
    private function sendWelcomeEmail($user, $password)
    {
        try {
            Mail::to($user['email'])->send(new WelcomeEmail($user, $password));
            Log::info('Welcome email sent successfully', [
                'user_id' => $user['pk'],
                'email' => $user['email']
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
}
