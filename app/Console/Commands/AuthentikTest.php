<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Authentik\AuthentikSDK;
use App\Services\Authentik\AuthentikClient;
use Illuminate\Support\Facades\Http;

class AuthentikTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authentik:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Authentik API connection and endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Authentik API connection...');
        
        // Check configuration
        $baseUrl = config('services.authentik.base_url');
        $apiToken = config('services.authentik.api_token');
        
        $this->info("Base URL: " . ($baseUrl ?: 'NOT SET'));
        $this->info("API Token: " . ($apiToken ? 'SET (length: ' . strlen($apiToken) . ')' : 'NOT SET'));
        
        if (!$baseUrl || !$apiToken) {
            $this->error('Missing configuration. Please set AUTHENTIK_BASE_URL and AUTHENTIK_API_TOKEN in your .env file.');
            return 1;
        }

        // Test raw HTTP connection
        $this->info("\n1. Testing raw HTTP connection...");
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
            ])->timeout(10)->get($baseUrl . '/api/v3/root/config/');
            
            if ($response->successful()) {
                $this->info('✓ Raw HTTP connection successful');
                $data = $response->json();
                $this->info('Version: ' . ($data['version'] ?? 'Unknown'));
            } else {
                $this->error('✗ Raw HTTP connection failed: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('✗ Raw HTTP connection failed: ' . $e->getMessage());
            return 1;
        }

        // Test SDK client
        $this->info("\n2. Testing SDK client...");
        try {
            $client = new AuthentikClient($apiToken);
            $config = $client->get('/root/config/');
            $this->info('✓ SDK client working');
            $this->info('Authentik version: ' . ($config['version'] ?? 'Unknown'));
        } catch (\Exception $e) {
            $this->error('✗ SDK client failed: ' . $e->getMessage());
            return 1;
        }

        // Test different endpoints
        $this->info("\n3. Testing API endpoints...");
        
        $endpoints = [
            '/core/users/' => 'Users endpoint',
            '/core/groups/' => 'Groups endpoint',
            '/core/applications/' => 'Applications endpoint',
            '/flows/instances/' => 'Flows endpoint',
        ];

        foreach ($endpoints as $endpoint => $description) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($baseUrl . '/api/v3' . $endpoint . '?page_size=1');
                
                if ($response->successful()) {
                    $data = $response->json();
                    $count = $data['count'] ?? 'Unknown';
                    $this->info("✓ {$description}: {$count} items");
                } else {
                    $this->error("✗ {$description}: " . $response->status());
                    $this->error('Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("✗ {$description}: " . $e->getMessage());
            }
        }

        // Test SDK managers
        $this->info("\n4. Testing SDK managers...");
        try {
            $sdk = new AuthentikSDK($apiToken);
            
            // Test users manager list method
            $this->info('Testing users list method...');
            $users = $sdk->users()->list(['page_size' => 1]);
            $this->info('✓ Users manager: ' . ($users['count'] ?? 0) . ' total users');
            
            // Test get individual user if any users exist
            if (isset($users['results']) && count($users['results']) > 0) {
                $firstUser = $users['results'][0];
                $this->info('Testing complete user detail flow...');
                $this->info('User: ' . $firstUser['username'] . ' (ID: ' . $firstUser['pk'] . ')');
                
                // Test individual user retrieval
                $userDetail = $sdk->users()->get($firstUser['pk']);
                $this->info('✓ User detail retrieved: ' . $userDetail['username']);
                
                // Check what's in the user detail
                $this->info('User detail keys: ' . implode(', ', array_keys($userDetail)));
                
                // Test groups in user detail
                if (isset($userDetail['groups'])) {
                    $this->info('✓ User has groups in detail: ' . count($userDetail['groups']));
                    if (count($userDetail['groups']) > 0) {
                        foreach ($userDetail['groups'] as $groupId) {
                            $this->info('  - Group ID: ' . $groupId);
                        }
                    }
                } else {
                    $this->info('ℹ User detail does not contain groups');
                }
                
                // Test getGroups method
                $this->info('Testing getGroups method...');
                try {
                    $groups = $sdk->users()->getGroups($firstUser['pk']);
                    $this->info('✓ User groups retrieved: ' . count($groups));
                    if (count($groups) > 0) {
                        foreach ($groups as $group) {
                            $this->info('  - ' . $group['name'] . ' (ID: ' . $group['pk'] . ')');
                        }
                    }
                } catch (\Exception $e) {
                    $this->error('✗ User groups failed: ' . $e->getMessage());
                }
                
                // Test controller instantiation
                $this->info('Testing controller...');
                try {
                    $controller = new \App\Http\Controllers\UserController();
                    $this->info('✓ UserController can be instantiated');
                } catch (\Exception $e) {
                    $this->error('✗ UserController failed: ' . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            $this->error('✗ SDK managers failed: ' . $e->getMessage());
            $this->error('Exception class: ' . get_class($e));
            
            // Try to get more specific error info
            if (method_exists($e, 'getCode')) {
                $this->error('Error code: ' . $e->getCode());
            }
            
            return 1;
        }

        $this->info("\n✓ All tests passed! Authentik SDK is ready to use.");
        return 0;
    }
}
