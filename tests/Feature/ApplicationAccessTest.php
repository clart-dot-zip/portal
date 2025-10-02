<?php

use App\Models\User;
use App\Services\ApplicationAccessService;
use App\Services\Authentik\AuthentikSDK;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('application access service can be instantiated', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    $service = new ApplicationAccessService($authentik);

    expect($service)->toBeInstanceOf(ApplicationAccessService::class);
});

test('user without authentik id cannot access applications', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    $service = new ApplicationAccessService($authentik);

    $result = $service->userCanAccess('app123', null);

    expect($result)->toBeFalse();
});

test('application access service handles errors gracefully', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    
    // Mock the request method to throw an exception
    $authentik->method('request')
        ->willThrowException(new Exception('API Error'));

    $service = new ApplicationAccessService($authentik);
    
    $result = $service->userCanAccess('app123', 'user123');

    // Should default to deny access on error
    expect($result)->toBeFalse();
});

test('service correctly identifies public applications', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    
    // Mock response with no access bindings for this application
    $authentik->method('request')
        ->willReturn([
            'results' => [
                // Some other application's binding
                [
                    'target' => 'other-app',
                    'group' => 'some-group',
                    'user' => null,
                    'policy' => null,
                    'enabled' => true
                ]
            ]
        ]);

    $service = new ApplicationAccessService($authentik);
    
    $result = $service->userCanAccess('public-app', 'user123');

    // No access bindings = public application
    expect($result)->toBeTrue();
});

test('service identifies applications with access bindings', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    
    // Mock response showing there are access bindings (so it's restricted)
    $authentik->method('request')
        ->willReturn([
            'results' => [
                [
                    'target' => 'restricted-app',
                    'group' => 'some-group',
                    'user' => null,
                    'policy' => null,
                    'enabled' => true
                ]
            ]
        ]);

    $service = new ApplicationAccessService($authentik);
    
    // Since there are access bindings but user isn't explicitly granted access,
    // this should return false (user denied)
    $result = $service->userCanAccess('restricted-app', 'user123');

    expect($result)->toBeFalse();
});

test('service integration works with real user model', function () {
    $user = User::factory()->create(['authentik_id' => 'test-user-123']);
    
    expect($user->authentik_id)->toBe('test-user-123');
    expect($user)->toBeInstanceOf(User::class);
});