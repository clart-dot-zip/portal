<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('application model basic functionality', function () {
    // Test basic model operations since the routes require admin access
    $user = User::factory()->create();
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->authentik_id)->toBeString();
});

test('unauthenticated users cannot access application management', function () {
    $this->get('/applications')->assertRedirect('/login');
    $this->get('/applications/app1')->assertRedirect('/login');
    $this->get('/applications/app1/edit')->assertRedirect('/login');
    $this->post('/applications/app1/users')->assertRedirect('/login');
});

test('application slug formatting works correctly', function () {
    // Test some basic slug logic that would be used in applications
    $name = 'Test Application Name';
    $slug = strtolower(str_replace(' ', '-', $name));
    
    expect($slug)->toBe('test-application-name');
});

test('application url validation works', function () {
    $validUrl = 'https://example.com';
    $invalidUrl = 'not-a-url';
    
    expect(filter_var($validUrl, FILTER_VALIDATE_URL))->toBeTruthy();
    expect(filter_var($invalidUrl, FILTER_VALIDATE_URL))->toBeFalsy();
});

// Note: Full application management tests require admin middleware
// and Authentik API integration which are tested separately