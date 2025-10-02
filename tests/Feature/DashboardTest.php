<?php

use App\Models\User;
use App\Services\ApplicationAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the ApplicationAccessService to avoid API calls in tests
    $this->mock(ApplicationAccessService::class, function ($mock) {
        $mock->shouldReceive('getUserAccessibleApplications')
            ->andReturn([
                [
                    'pk' => 'test-app-id',
                    'name' => 'Test Application',
                    'slug' => 'test-app',
                    'meta_description' => 'A test application',
                    'meta_launch_url' => 'https://test-app.example.com',
                    'meta_icon' => null,
                    'launch_url' => 'https://test-app.example.com'
                ]
            ]);
    });
});

test('dashboard can be accessed by authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.user');
    $response->assertViewHas('personalApps');
    $response->assertViewHas('userStats');
    $response->assertViewHas('isPortalAdmin');
});

test('dashboard redirects unauthenticated users', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('dashboard shows user applications', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Your Applications');
    $response->assertSee('Test Application');
});

test('home redirects authenticated users to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});

test('home shows welcome page for guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
});

// Note: Admin dashboard tests require middleware mocking which is complex
// Testing admin functionality would be done in integration tests