<?php

namespace App\Services\Authentik\Managers;

use App\Services\Authentik\AuthentikClient;

abstract class BaseManager
{
    protected AuthentikClient $client;

    public function __construct(AuthentikClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the base endpoint for this manager
     */
    abstract protected function getBaseEndpoint(): string;

    /**
     * List all resources
     */
    public function list(array $params = []): array
    {
        return $this->client->paginate($this->getBaseEndpoint(), $params);
    }

    /**
     * Get all resources (auto-paginated)
     */
    public function all(array $params = []): array
    {
        return $this->client->getAll($this->getBaseEndpoint(), $params);
    }

    /**
     * Get a specific resource by ID
     */
    public function get(string $id): array
    {
        return $this->client->get($this->getBaseEndpoint() . '/' . $id);
    }

    /**
     * Create a new resource
     */
    public function create(array $data): array
    {
        return $this->client->post($this->getBaseEndpoint(), $data);
    }

    /**
     * Update a resource
     */
    public function update(string $id, array $data): array
    {
        return $this->client->put($this->getBaseEndpoint() . '/' . $id, $data);
    }

    /**
     * Partially update a resource
     */
    public function patch(string $id, array $data): array
    {
        return $this->client->patch($this->getBaseEndpoint() . '/' . $id, $data);
    }

    /**
     * Delete a resource
     */
    public function delete(string $id): array
    {
        return $this->client->delete($this->getBaseEndpoint() . '/' . $id);
    }
}