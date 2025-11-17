<?php

namespace App\Services\Pterodactyl;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PterodactylClient
{
    private string $baseUrl;
    private ?string $apiKey;

    public function __construct()
    {
    $this->baseUrl = rtrim((string) (\config('pterodactyl.base_url') ?? ''), '/');
    $this->apiKey = \config('pterodactyl.api_key');

        if (empty($this->baseUrl) || empty($this->apiKey)) {
            throw new RuntimeException('Pterodactyl API is not configured. Please set PTERODACTYL_BASE_URL and PTERODACTYL_API_KEY.');
        }
    }

    /**
     * List servers visible to the application key.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listServers(bool $forceRefresh = false): array
    {
    $cacheTtl = (int) \config('pterodactyl.servers_cache_minutes', 5);
        $cacheKey = 'pterodactyl.application.servers';

        if ($forceRefresh || $cacheTtl === 0) {
            return $this->retrieveServers();
        }

        return Cache::remember($cacheKey, Carbon::now()->addMinutes($cacheTtl), function () {
            return $this->retrieveServers();
        });
    }

    /**
     * Retrieve a single server with optional relationships.
     */
    public function getServer($serverIdOrUuid, array $include = ['node']): array
    {
        $identifier = (string) $serverIdOrUuid;
        $endpoint = Str::isUuid($identifier)
            ? '/api/application/servers/external/' . $identifier
            : '/api/application/servers/' . $identifier;

        $response = $this->request()->get($endpoint, [
            'include' => implode(',', $include),
        ]);

        return $response->throw()->json('attributes', []);
    }

    /**
     * Retrieve node metadata for display purposes.
     */
    public function getNode(int $nodeId): array
    {
        $response = $this->request()->get('/api/application/nodes/' . $nodeId);

        return $response->throw()->json('attributes', []);
    }

    /**
     * Expose the low level HTTP client for bespoke calls.
     */
    public function request(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Fetch and paginate through the server list.
     *
     * @return array<int, array<string, mixed>>
     */
    private function retrieveServers(): array
    {
        $page = 1;
        $perPage = 50;
        $include = ['node'];
        $servers = [];

        do {
            $response = $this->request()->get('/api/application/servers', [
                'page' => $page,
                'per_page' => $perPage,
                'include' => implode(',', $include),
            ])->throw();

            $payload = $response->json();
            $data = Arr::get($payload, 'data', []);
            $meta = Arr::get($payload, 'meta.pagination', []);

            $servers = array_merge($servers, $data);
            $page++;
        } while (!empty($meta) && ($meta['current_page'] ?? ($page - 1)) < ($meta['total_pages'] ?? $meta['total_page'] ?? 1));

        return $servers;
    }
}
