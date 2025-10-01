<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentik\AuthentikSDK;
use App\Facades\Authentik;

class AuthentikController extends Controller
{
    protected AuthentikSDK $authentik;

    public function __construct(AuthentikSDK $authentik)
    {
        $this->authentik = $authentik;
    }

    /**
     * List all applications
     */
    public function applications()
    {
        try {
            $applications = $this->authentik->applications()->all();
            return response()->json($applications);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get application details
     */
    public function application($id)
    {
        try {
            $application = $this->authentik->applications()->get($id);
            return response()->json($application);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Create new application
     */
    public function createApplication(Request $request)
    {
        try {
            $application = $this->authentik->applications()->create($request->all());
            return response()->json($application, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List all users
     */
    public function users()
    {
        try {
            // Using facade
            $users = Authentik::users()->all();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search users
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = $request->get('q');
            $users = $this->authentik->users()->search($query);
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        try {
            $userData = $request->all();
            $password = $userData['password'] ?? null;
            unset($userData['password']);

            if ($password) {
                $user = $this->authentik->users()->createWithPassword($userData, $password);
            } else {
                $user = $this->authentik->users()->create($userData);
            }

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user groups
     */
    public function userGroups($userId)
    {
        try {
            $groups = $this->authentik->users()->getGroups($userId);
            return response()->json($groups);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add user to group
     */
    public function addUserToGroup(Request $request, $userId)
    {
        try {
            $groupId = $request->get('group_id');
            $result = $this->authentik->users()->addToGroup($userId, $groupId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List all groups
     */
    public function groups()
    {
        try {
            $groups = $this->authentik->groups()->all();
            return response()->json($groups);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get group members
     */
    public function groupMembers($groupId)
    {
        try {
            $members = $this->authentik->groups()->getMembers($groupId);
            return response()->json($members);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List all providers
     */
    public function providers()
    {
        try {
            $providers = $this->authentik->providers()->all();
            return response()->json($providers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get OAuth2 providers only
     */
    public function oauthProviders()
    {
        try {
            $providers = $this->authentik->providers()->getOAuth2();
            return response()->json($providers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List all flows
     */
    public function flows()
    {
        try {
            $flows = $this->authentik->flows()->all();
            return response()->json($flows);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get authentication flows
     */
    public function authFlows()
    {
        try {
            $flows = $this->authentik->flows()->getAuthenticationFlows();
            return response()->json($flows);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}