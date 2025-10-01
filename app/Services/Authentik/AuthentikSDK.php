<?php

namespace App\Services\Authentik;

use App\Services\Authentik\Managers\ApplicationManager;
use App\Services\Authentik\Managers\UserManager;
use App\Services\Authentik\Managers\GroupManager;
use App\Services\Authentik\Managers\FlowManager;
use App\Services\Authentik\Managers\ProviderManager;
use App\Services\Authentik\Managers\PolicyManager;
use App\Services\Authentik\Managers\PropertyMappingManager;
use App\Services\Authentik\Managers\SourceManager;
use App\Services\Authentik\Managers\TenantManager;

class AuthentikSDK
{
    protected AuthentikClient $client;

    public function __construct(?string $token = null)
    {
        $this->client = new AuthentikClient($token);
    }

    /**
     * Get the raw client for custom API calls
     */
    public function client(): AuthentikClient
    {
        return $this->client;
    }

    /**
     * Manage applications
     */
    public function applications(): ApplicationManager
    {
        return new ApplicationManager($this->client);
    }

    /**
     * Manage users
     */
    public function users(): UserManager
    {
        return new UserManager($this->client);
    }

    /**
     * Manage groups
     */
    public function groups(): GroupManager
    {
        return new GroupManager($this->client);
    }

    /**
     * Manage flows
     */
    public function flows(): FlowManager
    {
        return new FlowManager($this->client);
    }

    /**
     * Manage providers
     */
    public function providers(): ProviderManager
    {
        return new ProviderManager($this->client);
    }

    /**
     * Manage policies
     */
    public function policies(): PolicyManager
    {
        return new PolicyManager($this->client);
    }

    /**
     * Manage property mappings
     */
    public function propertyMappings(): PropertyMappingManager
    {
        return new PropertyMappingManager($this->client);
    }

    /**
     * Manage sources
     */
    public function sources(): SourceManager
    {
        return new SourceManager($this->client);
    }

    /**
     * Manage tenants
     */
    public function tenants(): TenantManager
    {
        return new TenantManager($this->client);
    }
}