<?php

namespace App\Services\Authentik\Managers;

class PropertyMappingManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/propertymappings/all/';
    }

    /**
     * Get SAML property mappings
     */
    public function getSAMLMappings(): array
    {
        return $this->client->get('/propertymappings/saml');
    }

    /**
     * Get OAuth2/OIDC property mappings
     */
    public function getOAuth2Mappings(): array
    {
        return $this->client->get('/propertymappings/scope');
    }

    /**
     * Get LDAP property mappings
     */
    public function getLDAPMappings(): array
    {
        return $this->client->get('/propertymappings/ldap');
    }

    /**
     * Get SCIM property mappings
     */
    public function getSCIMMappings(): array
    {
        return $this->client->get('/propertymappings/scim');
    }

    /**
     * Test property mapping
     */
    public function test(string $mappingId, array $context = []): array
    {
        return $this->client->post("/propertymappings/all/{$mappingId}/test", $context);
    }

    /**
     * Get property mapping's used by relationships
     */
    public function getUsedBy(string $mappingId): array
    {
        return $this->client->get("/propertymappings/all/{$mappingId}/used_by");
    }

    /**
     * Create SAML property mapping
     */
    public function createSAMLMapping(array $data): array
    {
        return $this->client->post('/propertymappings/saml', $data);
    }

    /**
     * Create OAuth2/OIDC scope mapping
     */
    public function createScopeMapping(array $data): array
    {
        return $this->client->post('/propertymappings/scope', $data);
    }

    /**
     * Search property mappings
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get mappings by name
     */
    public function getByName(string $name): array
    {
        $mappings = $this->list(['name' => $name]);
        
        if (empty($mappings['results'])) {
            throw new \Exception("Property mapping with name '{$name}' not found");
        }
        
        return $mappings['results'][0];
    }
}