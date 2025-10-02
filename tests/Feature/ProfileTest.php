<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users can access their own profile when authenticated', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertStatus(200);
    $response->assertViewIs('users.profile');
    $response->assertSee('Test User');
});

test('users can update their own profile', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com'
    ]);

    $response = $this->actingAs($user)->put('/profile', [
        'name' => 'New Name',
        'email' => 'new@example.com'
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success', 'Profile updated successfully!');
});

test('profile update validates required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put('/profile', [
        'name' => '',
        'email' => ''
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('unauthenticated users cannot access profile', function () {
    $this->get('/profile')->assertRedirect('/login');
    $this->put('/profile')->assertRedirect('/login');
});