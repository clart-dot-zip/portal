<?php

use Tests\TestCase;
use App\Services\Authentik\AuthentikSDK;
use App\Services\Authentik\Exceptions\AuthentikException;

class AuthentikSDKTest extends TestCase
{
    protected AuthentikSDK $authentik;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the configuration for testing
        config([
            'services.authentik.base_url' => 'https://test-authentik.example.com',
            'services.authentik.api_token' => 'test-token'
        ]);
        
        $this->authentik = new AuthentikSDK();
    }

    /** @test */
    public function it_can_instantiate_sdk()
    {
        $this->assertInstanceOf(AuthentikSDK::class, $this->authentik);
    }

    /** @test */
    public function it_provides_manager_instances()
    {
        $this->assertInstanceOf(
            \App\Services\Authentik\Managers\ApplicationManager::class,
            $this->authentik->applications()
        );

        $this->assertInstanceOf(
            \App\Services\Authentik\Managers\UserManager::class,
            $this->authentik->users()
        );

        $this->assertInstanceOf(
            \App\Services\Authentik\Managers\GroupManager::class,
            $this->authentik->groups()
        );
    }

    /** @test */
    public function it_throws_exception_for_missing_config()
    {
        config(['services.authentik.base_url' => null]);

        $this->expectException(AuthentikException::class);
        $this->expectExceptionMessage('Authentik base URL is not configured');

        new AuthentikSDK();
    }

    /** @test */
    public function it_provides_raw_client_access()
    {
        $client = $this->authentik->client();
        
        $this->assertInstanceOf(
            \App\Services\Authentik\AuthentikClient::class,
            $client
        );
    }
}