<?php

namespace App\Services\Authentik\Managers;

class ApplicationManager extends BaseManager
{
    protected function getBaseEndpoint(): string
    {
        return '/core/applications/';
    }

    /**
     * Get application by slug
     */
    public function getBySlug(string $slug): array
    {
        $applications = $this->list(['slug' => $slug]);
        
        if (empty($applications['results'])) {
            throw new \Exception("Application with slug '{$slug}' not found");
        }
        
        return $applications['results'][0];
    }

    /**
     * Get application metrics
     */
    public function getMetrics(string $id): array
    {
        return $this->client->get("/core/applications/{$id}/metrics");
    }

    /**
     * Check if application is available
     */
    public function checkAvailable(string $id): array
    {
        return $this->client->get("/core/applications/{$id}/check_access");
    }

    /**
     * Get application's used by relationships
     */
    public function getUsedBy(string $id): array
    {
        return $this->client->get("/core/applications/{$id}/used_by");
    }

    /**
     * Set application icon
     */
    public function setIcon(string $id, string $iconPath): array
    {
        // Note: This would need multipart/form-data handling for file uploads
        // For now, we'll assume the icon is a URL or base64 string
        return $this->patch($id, ['meta_icon' => $iconPath]);
    }

    /**
     * Get applications that the current user can access
     */
    public function getAccessible(): array
    {
        return $this->client->get('/core/applications', ['superuser_full_list' => 'false']);
    }

    /**
     * Search applications by name
     */
    public function search(string $query): array
    {
        return $this->list(['search' => $query]);
    }

    /**
     * Get applications by provider type
     */
    public function getByProviderType(string $providerType): array
    {
        return $this->list(['provider__isnull' => 'false']);
    }

    /**
     * Get applications without providers
     */
    public function getWithoutProviders(): array
    {
        return $this->list(['provider__isnull' => 'true']);
    }
}