<?php

namespace App\Services\Authentik\Managers;

class ProviderManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/providers/all/';
    }

    /**
     * Get OAuth2 providers
     */
    public function getOAuth2(): array
    {
        return $this->client->get('/providers/oauth2');
    }

    /**
     * Get SAML providers
     */
    public function getSAML(): array
    {
        return $this->client->get('/providers/saml');
    }

    /**
     * Get Proxy providers
     */
    public function getProxy(): array
    {
        return $this->client->get('/providers/proxy');
    }

    /**
     * Get LDAP providers
     */
    public function getLDAP(): array
    {
        return $this->client->get('/providers/ldap');
    }

    /**
     * Get SCIM providers
     */
    public function getSCIM(): array
    {
        return $this->client->get('/providers/scim');
    }

    /**
     * Get RADIUS providers
     */
    public function getRADIUS(): array
    {
        return $this->client->get('/providers/radius');
    }

    /**
     * Create OAuth2 provider
     */
    public function createOAuth2(array $data): array
    {
        return $this->client->post('/providers/oauth2', $data);
    }

    /**
     * Create SAML provider
     */
    public function createSAML(array $data): array
    {
        return $this->client->post('/providers/saml', $data);
    }

    /**
     * Create Proxy provider
     */
    public function createProxy(array $data): array
    {
        return $this->client->post('/providers/proxy', $data);
    }

    /**
     * Get provider by name
     */
    public function getByName(string $name): array
    {
        $providers = $this->list(['name' => $name]);
        
        if (empty($providers['results'])) {
            throw new \Exception("Provider with name '{$name}' not found");
        }
        
        return $providers['results'][0];
    }

    /**
     * Get provider metrics
     */
    public function getMetrics(string $providerId): array
    {
        return $this->client->get("/providers/all/{$providerId}/metrics");
    }

    /**
     * Get provider's used by relationships
     */
    public function getUsedBy(string $providerId): array
    {
        return $this->client->get("/providers/all/{$providerId}/used_by");
    }

    /**
     * Search providers
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }
}