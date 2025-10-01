<?php

namespace App\Services\Authentik\Managers;

class UserManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/core/users/';
    }

    /**
     * Get user by username
     */
    public function getByUsername(string $username): array
    {
        $users = $this->list(['username' => $username]);
        
        if (empty($users['results'])) {
            throw new \Exception("User with username '{$username}' not found");
        }
        
        return $users['results'][0];
    }

    /**
     * Get user by email
     */
    public function getByEmail(string $email): array
    {
        $users = $this->list(['email' => $email]);
        
        if (empty($users['results'])) {
            throw new \Exception("User with email '{$email}' not found");
        }
        
        return $users['results'][0];
    }

    /**
     * Search users
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->list(['is_active' => 'true']);
    }

    /**
     * Get inactive users
     */
    public function getInactive(): array
    {
        return $this->list(['is_active' => 'false']);
    }

    /**
     * Get superuser accounts
     */
    public function getSuperusers(): array
    {
        return $this->list(['is_superuser' => 'true']);
    }

    /**
     * Get users by group
     */
    public function getByGroup(string $groupId): array
    {
        return $this->list(['groups' => $groupId]);
    }

    /**
     * Set user password
     */
    public function setPassword(string $userId, string $password): array
    {
        return $this->client->post("/core/users/{$userId}/set_password/", [
            'password' => $password
        ]);
    }

    /**
     * Add user to group
     */
    public function addToGroup(string $userId, string $groupId): array
    {
        return $this->client->post("/core/users/{$userId}/add_to_group/", [
            'group' => $groupId
        ]);
    }

    /**
     * Remove user from group
     */
    public function removeFromGroup(string $userId, string $groupId): array
    {
        return $this->client->post("/core/users/{$userId}/remove_from_group/", [
            'group' => $groupId
        ]);
    }

    /**
     * Get user's groups (alternative method using groups endpoint)
     */
    public function getGroups(string $userId): array
    {
        // Try the direct user groups endpoint first
        try {
            return $this->client->get("/core/users/{$userId}/groups/");
        } catch (\Exception $e) {
            // If that fails, try getting groups and filtering by user
            try {
                $allGroups = $this->client->get("/core/groups/");
                $userGroups = [];
                
                if (isset($allGroups['results'])) {
                    foreach ($allGroups['results'] as $group) {
                        // Check if this group contains our user
                        try {
                            $members = $this->client->get("/core/groups/{$group['pk']}/users/");
                            if (isset($members['results'])) {
                                foreach ($members['results'] as $member) {
                                    if ($member['pk'] === $userId) {
                                        $userGroups[] = $group;
                                        break;
                                    }
                                }
                            }
                        } catch (\Exception $memberException) {
                            // Skip this group if we can't get members
                            continue;
                        }
                    }
                }
                
                return $userGroups;
            } catch (\Exception $fallbackException) {
                // If all methods fail, return empty array
                return [];
            }
        }
    }

    /**
     * Get user sessions
     */
    public function getSessions(string $userId): array
    {
        return $this->client->get("/core/users/{$userId}/sessions/");
    }

    /**
     * Terminate all user sessions
     */
    public function terminateSessions(string $userId): array
    {
        return $this->client->post("/core/users/{$userId}/logout/", []);
    }

    /**
     * Get user's permissions
     */
    public function getPermissions(string $userId): array
    {
        return $this->client->get("/core/users/{$userId}/permissions/");
    }

    /**
     * Get user metrics
     */
    public function getMetrics(string $userId): array
    {
        return $this->client->get("/core/users/{$userId}/metrics/");
    }

    /**
     * Send recovery email
     */
    public function sendRecoveryEmail(string $userId): array
    {
        return $this->client->post("/core/users/{$userId}/recovery/", []);
    }

    /**
     * Get user's used by relationships
     */
    public function getUsedBy(string $userId): array
    {
        return $this->client->get("/core/users/{$userId}/used_by/");
    }

    /**
     * Impersonate user (requires appropriate permissions)
     */
    public function impersonate(string $userId): array
    {
        return $this->client->post("/core/users/{$userId}/impersonate/", []);
    }

    /**
     * Activate user account
     */
    public function activate(string $userId): array
    {
        return $this->patch($userId, ['is_active' => true]);
    }

    /**
     * Deactivate user account
     */
    public function deactivate(string $userId): array
    {
        return $this->patch($userId, ['is_active' => false]);
    }

    /**
     * Create user with password
     */
    public function createWithPassword(array $userData, string $password): array
    {
        $user = $this->create($userData);
        $this->setPassword($user['pk'], $password);
        return $user;
    }
}