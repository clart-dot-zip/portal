<?php

use App\Models\User;
use App\Services\Authentik\AuthentikSDK;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user factory creates users with proper attributes', function () {
    $user = User::factory()->create();

    expect($user->authentik_id)->toBeString();
    expect($user->username)->toBeString();
    expect($user->name)->toBeString();
    expect($user->email)->toContain('@');
    expect($user->is_active)->toBeBool();
});

test('admin user factory creates admin attributes', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->authentik_attributes)->toHaveKey('portal_admin');
    expect($admin->authentik_attributes['portal_admin'])->toBeTrue();
});

test('inactive user factory creates inactive users', function () {
    $inactiveUser = User::factory()->inactive()->create();

    expect($inactiveUser->is_active)->toBeFalse();
});

test('users can be found by authentik id', function () {
    $user = User::factory()->create(['authentik_id' => 'test-authentik-id']);

    $foundUser = User::where('authentik_id', 'test-authentik-id')->first();

    expect($foundUser)->not->toBeNull();
    expect($foundUser->authentik_id)->toBe('test-authentik-id');
});

test('user model has expected fillable attributes', function () {
    $user = new User();
    
    $fillable = $user->getFillable();
    
    expect($fillable)->toContain('authentik_id');
    expect($fillable)->toContain('username');
    expect($fillable)->toContain('name');
    expect($fillable)->toContain('email');
    expect($fillable)->toContain('is_active');
    expect($fillable)->toContain('authentik_attributes');
});

// Note: Routes that require admin access are tested separately
// since they need special middleware handling