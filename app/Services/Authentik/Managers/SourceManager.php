<?php

namespace App\Services\Authentik\Managers;

class SourceManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/sources/all/';
    }

    /**
     * Get OAuth sources
     */
    public function getOAuthSources(): array
    {
        return $this->client->get('/sources/oauth');
    }

    /**
     * Get SAML sources
     */
    public function getSAMLSources(): array
    {
        return $this->client->get('/sources/saml');
    }

    /**
     * Get LDAP sources
     */
    public function getLDAPSources(): array
    {
        return $this->client->get('/sources/ldap');
    }

    /**
     * Get file sources
     */
    public function getFileSources(): array
    {
        return $this->client->get('/sources/user_file');
    }

    /**
     * Get Plex sources
     */
    public function getPlexSources(): array
    {
        return $this->client->get('/sources/plex');
    }

    /**
     * Create OAuth source
     */
    public function createOAuthSource(array $data): array
    {
        return $this->client->post('/sources/oauth', $data);
    }

    /**
     * Create SAML source
     */
    public function createSAMLSource(array $data): array
    {
        return $this->client->post('/sources/saml', $data);
    }

    /**
     * Create LDAP source
     */
    public function createLDAPSource(array $data): array
    {
        return $this->client->post('/sources/ldap', $data);
    }

    /**
     * Sync LDAP source
     */
    public function syncLDAP(string $sourceId): array
    {
        return $this->client->post("/sources/ldap/{$sourceId}/sync", []);
    }

    /**
     * Get source's used by relationships
     */
    public function getUsedBy(string $sourceId): array
    {
        return $this->client->get("/sources/all/{$sourceId}/used_by");
    }

    /**
     * Get source by slug
     */
    public function getBySlug(string $slug): array
    {
        $sources = $this->list(['slug' => $slug]);
        
        if (empty($sources['results'])) {
            throw new \Exception("Source with slug '{$slug}' not found");
        }
        
        return $sources['results'][0];
    }

    /**
     * Search sources
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get enabled sources
     */
    public function getEnabled(): array
    {
        return $this->list(['enabled' => 'true']);
    }

    /**
     * Get disabled sources
     */
    public function getDisabled(): array
    {
        return $this->list(['enabled' => 'false']);
    }
}