<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel properly
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Authentik\AuthentikSDK;

try {
    $sdk = new AuthentikSDK(config('services.authentik.api_token'));
    
    echo "=== Testing Recovery Link Approach ===" . PHP_EOL;
    
    $testUsername = 'recovery-test-' . time();
    $testEmail = 'recovery-test-' . time() . '@example.com';
    
    echo "Creating user: " . $testUsername . PHP_EOL;
    
    // Create user
    $user = $sdk->users()->create([
        'username' => $testUsername,
        'email' => $testEmail,
        'name' => 'Recovery Test User',
        'is_active' => true,
        'password_change_date' => now()->subDay()->toISOString() // Force password change
    ]);
    
    echo "✓ User created with ID: " . $user['pk'] . PHP_EOL;
    
    // Generate recovery link
    $recovery = $sdk->request('POST', "/core/users/{$user['pk']}/recovery/", []);
    
    if (isset($recovery['link'])) {
        echo "✓ Recovery link generated!" . PHP_EOL;
        echo "Recovery URL: " . $recovery['link'] . PHP_EOL;
        echo PHP_EOL;
        echo "WORKFLOW TEST:" . PHP_EOL;
        echo "1. User receives email with this recovery link" . PHP_EOL;
        echo "2. User clicks link to set their password" . PHP_EOL;
        echo "3. User can then login normally with their new password" . PHP_EOL;
        echo PHP_EOL;
        echo "This approach is working! ✅" . PHP_EOL;
    } else {
        echo "✗ Failed to generate recovery link" . PHP_EOL;
    }
    
    echo PHP_EOL . "Delete test user? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 'y') {
        $sdk->users()->delete($user['pk']);
        echo "✓ Test user deleted" . PHP_EOL;
    } else {
        echo "✓ Test user kept: " . $testUsername . PHP_EOL;
        echo "Test the recovery link: " . $recovery['link'] . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}