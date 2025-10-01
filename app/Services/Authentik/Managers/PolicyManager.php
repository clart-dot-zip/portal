<?php

namespace App\Services\Authentik\Managers;

class PolicyManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/policies/all/';
    }

    /**
     * Get policies by type
     */
    public function getByType(string $type): array
    {
        return $this->list(['__type' => $type]);
    }

    /**
     * Get expression policies
     */
    public function getExpressionPolicies(): array
    {
        return $this->client->get('/policies/expression');
    }

    /**
     * Get group membership policies
     */
    public function getGroupMembershipPolicies(): array
    {
        return $this->client->get('/policies/group_membership');
    }

    /**
     * Get password policies
     */
    public function getPasswordPolicies(): array
    {
        return $this->client->get('/policies/password');
    }

    /**
     * Get reputation policies
     */
    public function getReputationPolicies(): array
    {
        return $this->client->get('/policies/reputation');
    }

    /**
     * Test policy
     */
    public function test(string $policyId, array $context = []): array
    {
        return $this->client->post("/policies/all/{$policyId}/test", $context);
    }

    /**
     * Get policy's used by relationships
     */
    public function getUsedBy(string $policyId): array
    {
        return $this->client->get("/policies/all/{$policyId}/used_by");
    }

    /**
     * Create expression policy
     */
    public function createExpressionPolicy(array $data): array
    {
        return $this->client->post('/policies/expression', $data);
    }

    /**
     * Create group membership policy
     */
    public function createGroupMembershipPolicy(array $data): array
    {
        return $this->client->post('/policies/group_membership', $data);
    }

    /**
     * Search policies
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }
}