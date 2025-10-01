<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel properly
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Authentik\AuthentikSDK;

try {
    $sdk = new AuthentikSDK(config('services.authentik.api_token'));
    
    echo "=== Investigating Recovery Link Parameters ===" . PHP_EOL;
    
    $testUsername = 'recovery-params-' . time();
    $testEmail = 'recovery-params-' . time() . '@example.com';
    
    echo "Creating test user: " . $testUsername . PHP_EOL;
    
    // Create user
    $user = $sdk->users()->create([
        'username' => $testUsername,
        'email' => $testEmail,
        'name' => 'Recovery Params Test User',
        'is_active' => true,
        'password_change_date' => now()->subDay()->toISOString()
    ]);
    
    echo "✓ User created with ID: " . $user['pk'] . PHP_EOL;
    
    // Test different recovery parameters
    echo PHP_EOL . "=== Testing Recovery Parameters ===" . PHP_EOL;
    
    // Method 1: Basic recovery (current method)
    echo "Method 1: Basic recovery (no parameters)" . PHP_EOL;
    try {
        $recovery1 = $sdk->request('POST', "/core/users/{$user['pk']}/recovery/", []);
        if (isset($recovery1['link'])) {
            echo "✓ Basic recovery link: " . $recovery1['link'] . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo "✗ Basic recovery failed: " . $e->getMessage() . PHP_EOL;
    }
    
    // Method 2: Try with email stage parameter
    echo PHP_EOL . "Method 2: With email_stage parameter" . PHP_EOL;
    try {
        $recovery2 = $sdk->request('POST', "/core/users/{$user['pk']}/recovery/", [
            'email_stage' => 'default-recovery-email'
        ]);
        if (isset($recovery2['link'])) {
            echo "✓ Email stage recovery link: " . $recovery2['link'] . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo "✗ Email stage recovery failed: " . $e->getMessage() . PHP_EOL;
    }
    
    // Method 3: Try with user information
    echo PHP_EOL . "Method 3: With user information" . PHP_EOL;
    try {
        $recovery3 = $sdk->request('POST', "/core/users/{$user['pk']}/recovery/", [
            'username' => $testUsername,
            'email' => $testEmail
        ]);
        if (isset($recovery3['link'])) {
            echo "✓ User info recovery link: " . $recovery3['link'] . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo "✗ User info recovery failed: " . $e->getMessage() . PHP_EOL;
    }
    
    // Method 4: Check if we can use a different flow
    echo PHP_EOL . "Method 4: Investigating available flows" . PHP_EOL;
    try {
        $flows = $sdk->request('GET', '/flows/instances/');
        echo "Available flows: " . count($flows['results']) . PHP_EOL;
        
        foreach ($flows['results'] as $flow) {
            if (strpos(strtolower($flow['name']), 'recovery') !== false || 
                strpos(strtolower($flow['slug']), 'recovery') !== false) {
                echo "Recovery flow found: " . $flow['name'] . " (slug: " . $flow['slug'] . ")" . PHP_EOL;
            }
        }
    } catch (\Exception $e) {
        echo "✗ Flows investigation failed: " . $e->getMessage() . PHP_EOL;
    }
    
    // Method 5: Try creating a direct password reset link
    echo PHP_EOL . "Method 5: Alternative - Direct password reset" . PHP_EOL;
    try {
        // Check if we can create a direct link with user context
        $resetData = [
            'user' => $user['pk'],
            'flow' => 'default-recovery-flow'
        ];
        
        $directReset = $sdk->request('POST', '/flows/executor/default-recovery-flow/', $resetData);
        echo "Direct reset response: " . json_encode($directReset) . PHP_EOL;
    } catch (\Exception $e) {
        echo "✗ Direct reset failed: " . $e->getMessage() . PHP_EOL;
    }
    
    // Method 6: Check URL parameters that might work
    echo PHP_EOL . "Method 6: URL Parameter Analysis" . PHP_EOL;
    $basicLink = $recovery1['link'] ?? '';
    if ($basicLink) {
        $parsedUrl = parse_url($basicLink);
        echo "Base URL: " . $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . PHP_EOL;
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);
            echo "Current parameters: " . print_r($params, true) . PHP_EOL;
            
            // Try adding username/email parameters
            $enhancedParams = array_merge($params, [
                'email' => $testEmail,
                'username' => $testUsername
            ]);
            
            $enhancedUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . http_build_query($enhancedParams);
            echo "Enhanced URL with user data: " . $enhancedUrl . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "Delete test user? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 'y') {
        $sdk->users()->delete($user['pk']);
        echo "✓ Test user deleted" . PHP_EOL;
    } else {
        echo "✓ Test user kept for manual testing" . PHP_EOL;
        echo "Test the links above to see which works best!" . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}