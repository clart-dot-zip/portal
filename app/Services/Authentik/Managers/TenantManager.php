<?php

namespace App\Services\Authentik\Managers;

class TenantManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/core/tenants/';
    }

    /**
     * Get current tenant
     */
    public function getCurrent(): array
    {
        return $this->client->get('/core/tenants/current');
    }

    /**
     * Get tenant by domain
     */
    public function getByDomain(string $domain): array
    {
        $tenants = $this->list(['domain' => $domain]);
        
        if (empty($tenants['results'])) {
            throw new \Exception("Tenant with domain '{$domain}' not found");
        }
        
        return $tenants['results'][0];
    }

    /**
     * Get default tenant
     */
    public function getDefault(): array
    {
        $tenants = $this->list(['default' => 'true']);
        
        if (empty($tenants['results'])) {
            throw new \Exception("No default tenant found");
        }
        
        return $tenants['results'][0];
    }

    /**
     * Search tenants
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get tenant's used by relationships
     */
    public function getUsedBy(string $tenantId): array
    {
        return $this->client->get("/core/tenants/{$tenantId}/used_by");
    }
}