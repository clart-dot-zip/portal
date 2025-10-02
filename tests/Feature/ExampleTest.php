<?php

test('welcome page loads successfully for guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
    $response->assertSee('Welcome to Portal');
});

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
