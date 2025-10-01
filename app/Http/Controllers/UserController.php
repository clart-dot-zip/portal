<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
}
