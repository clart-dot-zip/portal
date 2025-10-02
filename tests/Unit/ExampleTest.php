<?php

test('basic math operations work correctly', function () {
    expect(2 + 2)->toBe(4);
    expect(5 * 3)->toBe(15);
    expect(10 / 2)->toBe(5);
});

test('string manipulation works correctly', function () {
    $name = 'Test Application Name';
    $slug = strtolower(str_replace(' ', '-', $name));
    
    expect($slug)->toBe('test-application-name');
});

test('array operations work correctly', function () {
    $data = ['name' => 'Test', 'value' => 123];
    
    expect($data)->toHaveKey('name');
    expect($data['name'])->toBe('Test');
    expect($data['value'])->toBe(123);
});

// Note: User factory tests are in Feature tests where database is available
