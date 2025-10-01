<?php

namespace App\Services\Authentik\Managers;

class GroupManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/core/groups/';
    }

    /**
     * Get group by name
     */
    public function getByName(string $name): array
    {
        $groups = $this->list(['name' => $name]);
        
        if (empty($groups['results'])) {
            throw new \Exception("Group with name '{$name}' not found");
        }
        
        return $groups['results'][0];
    }

    /**
     * Search groups
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get group members (alias for getMembers)
     */
    public function getUsers(string $groupId): array
    {
        return $this->getMembers($groupId);
    }

    /**
     * Get group members
     */
    public function getMembers(string $groupId): array
    {
        try {
            // Use the correct endpoint to get users by group
            $result = $this->client->get("/core/users/?groups={$groupId}");
            return $result['results'] ?? $result;
        } catch (\Exception $e) {
            // Fallback: return empty array
            return [];
        }
    }

    /**
     * Add user to group
     */
    public function addUser(string $groupId, string $userId): array
    {
        return $this->client->post("/core/groups/{$groupId}/add_user", [
            'user' => $userId
        ]);
    }

    /**
     * Remove user from group
     */
    public function removeUser(string $groupId, string $userId): array
    {
        return $this->client->post("/core/groups/{$groupId}/remove_user", [
            'user' => $userId
        ]);
    }

    /**
     * Get groups by parent
     */
    public function getByParent(string $parentId): array
    {
        return $this->list(['parent' => $parentId]);
    }

    /**
     * Get root groups (no parent)
     */
    public function getRootGroups(): array
    {
        return $this->list(['parent__isnull' => 'true']);
    }

    /**
     * Get group's used by relationships
     */
    public function getUsedBy(string $groupId): array
    {
        return $this->client->get("/core/groups/{$groupId}/used_by");
    }

    /**
     * Get superuser groups
     */
    public function getSuperuserGroups(): array
    {
        return $this->list(['is_superuser' => 'true']);
    }

    /**
     * Bulk add users to group
     */
    public function bulkAddUsers(string $groupId, array $userIds): array
    {
        $results = [];
        foreach ($userIds as $userId) {
            $results[] = $this->addUser($groupId, $userId);
        }
        return $results;
    }

    /**
     * Bulk remove users from group
     */
    public function bulkRemoveUsers(string $groupId, array $userIds): array
    {
        $results = [];
        foreach ($userIds as $userId) {
            $results[] = $this->removeUser($groupId, $userId);
        }
        return $results;
    }
}