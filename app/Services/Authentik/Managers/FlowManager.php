<?php

namespace App\Services\Authentik\Managers;

class FlowManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/flows/instances/';
    }

    /**
     * Get flow by slug
     */
    public function getBySlug(string $slug): array
    {
        $flows = $this->list(['slug' => $slug]);
        
        if (empty($flows['results'])) {
            throw new \Exception("Flow with slug '{$slug}' not found");
        }
        
        return $flows['results'][0];
    }

    /**
     * Get flows by designation
     */
    public function getByDesignation(string $designation): array
    {
        return $this->list(['designation' => $designation]);
    }

    /**
     * Get authentication flows
     */
    public function getAuthenticationFlows(): array
    {
        return $this->getByDesignation('authentication');
    }

    /**
     * Get authorization flows
     */
    public function getAuthorizationFlows(): array
    {
        return $this->getByDesignation('authorization');
    }

    /**
     * Get enrollment flows
     */
    public function getEnrollmentFlows(): array
    {
        return $this->getByDesignation('enrollment');
    }

    /**
     * Get invalidation flows
     */
    public function getInvalidationFlows(): array
    {
        return $this->getByDesignation('invalidation');
    }

    /**
     * Get recovery flows
     */
    public function getRecoveryFlows(): array
    {
        return $this->getByDesignation('recovery');
    }

    /**
     * Execute flow
     */
    public function execute(string $flowSlug, array $data = []): array
    {
        return $this->client->post("/flows/executor/{$flowSlug}", $data);
    }

    /**
     * Get flow diagram
     */
    public function getDiagram(string $flowId): array
    {
        return $this->client->get("/flows/instances/{$flowId}/diagram");
    }

    /**
     * Export flow
     */
    public function export(string $flowId): array
    {
        return $this->client->get("/flows/instances/{$flowId}/export");
    }

    /**
     * Import flow
     */
    public function import(array $flowData): array
    {
        return $this->client->post('/flows/instances/import', $flowData);
    }

    /**
     * Get flow's used by relationships
     */
    public function getUsedBy(string $flowId): array
    {
        return $this->client->get("/flows/instances/{$flowId}/used_by");
    }

    /**
     * Cache flow
     */
    public function cache(string $flowId): array
    {
        return $this->client->post("/flows/instances/{$flowId}/cache", []);
    }

    /**
     * Clear flow cache
     */
    public function clearCache(string $flowId): array
    {
        return $this->client->delete("/flows/instances/{$flowId}/cache");
    }

    /**
     * Search flows
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get flow stages
     */
    public function getStages(string $flowId): array
    {
        return $this->client->get("/flows/bindings", ['target' => $flowId]);
    }
}