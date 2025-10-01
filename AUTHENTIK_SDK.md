# Authentik SDK for Laravel

A comprehensive Laravel SDK for managing Authentik through its REST API. This SDK provides easy-to-use interfaces for managing users, applications, groups, providers, flows, and more.

## Installation & Setup

### 1. Environment Configuration

Add the following environment variables to your `.env` file:

```env
AUTHENTIK_BASE_URL=https://your-authentik-instance.com
AUTHENTIK_CLIENT_ID=your_client_id
AUTHENTIK_CLIENT_SECRET=your_client_secret
AUTHENTIK_REDIRECT_URI=http://localhost:8000/auth/callback
AUTHENTIK_API_TOKEN=your_api_token
```

### 2. API Token Generation

To get an API token:

1. Log into your Authentik admin interface
2. Go to **Applications** → **Tokens**
3. Create a new token with appropriate permissions
4. Copy the token to your `AUTHENTIK_API_TOKEN` environment variable

### 3. Service Provider

The `AuthentikServiceProvider` is automatically registered in `bootstrap/providers.php`.

## Usage

### Basic Usage

You can use the SDK in three ways:

#### 1. Dependency Injection

```php
use App\Services\Authentik\AuthentikSDK;

class MyController extends Controller
{
    protected AuthentikSDK $authentik;

    public function __construct(AuthentikSDK $authentik)
    {
        $this->authentik = $authentik;
    }

    public function listUsers()
    {
        $users = $this->authentik->users()->all();
        return response()->json($users);
    }
}
```

#### 2. Facade

```php
use App\Facades\Authentik;

$users = Authentik::users()->all();
$applications = Authentik::applications()->list();
```

#### 3. Service Container

```php
$authentik = app(AuthentikSDK::class);
$users = $authentik->users()->all();
```

## Available Managers

### Applications Manager

```php
// List all applications
$applications = $authentik->applications()->all();

// Get specific application
$app = $authentik->applications()->get('app-id');

// Get application by slug
$app = $authentik->applications()->getBySlug('my-app');

// Create new application
$newApp = $authentik->applications()->create([
    'name' => 'My App',
    'slug' => 'my-app',
    'provider' => 'provider-id'
]);

// Update application
$updatedApp = $authentik->applications()->update('app-id', [
    'name' => 'Updated App Name'
]);

// Delete application
$authentik->applications()->delete('app-id');

// Get application metrics
$metrics = $authentik->applications()->getMetrics('app-id');

// Search applications
$results = $authentik->applications()->search('search-term');
```

### User Manager

```php
// List all users
$users = $authentik->users()->all();

// Get user by ID
$user = $authentik->users()->get('user-id');

// Get user by username
$user = $authentik->users()->getByUsername('john_doe');

// Get user by email
$user = $authentik->users()->getByEmail('john@example.com');

// Create new user
$newUser = $authentik->users()->create([
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'name' => 'John Doe',
    'is_active' => true
]);

// Create user with password
$newUser = $authentik->users()->createWithPassword([
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'name' => 'John Doe'
], 'secure_password');

// Set user password
$authentik->users()->setPassword('user-id', 'new_password');

// Add user to group
$authentik->users()->addToGroup('user-id', 'group-id');

// Remove user from group
$authentik->users()->removeFromGroup('user-id', 'group-id');

// Get user's groups
$groups = $authentik->users()->getGroups('user-id');

// Get user sessions
$sessions = $authentik->users()->getSessions('user-id');

// Terminate all user sessions
$authentik->users()->terminateSessions('user-id');

// Activate/deactivate user
$authentik->users()->activate('user-id');
$authentik->users()->deactivate('user-id');

// Search users
$results = $authentik->users()->search('search-term');

// Get active/inactive users
$activeUsers = $authentik->users()->getActive();
$inactiveUsers = $authentik->users()->getInactive();

// Get superusers
$superusers = $authentik->users()->getSuperusers();
```

### Group Manager

```php
// List all groups
$groups = $authentik->groups()->all();

// Get group by ID
$group = $authentik->groups()->get('group-id');

// Get group by name
$group = $authentik->groups()->getByName('Admin Group');

// Create new group
$newGroup = $authentik->groups()->create([
    'name' => 'New Group',
    'is_superuser' => false
]);

// Get group members
$members = $authentik->groups()->getMembers('group-id');

// Add user to group
$authentik->groups()->addUser('group-id', 'user-id');

// Remove user from group
$authentik->groups()->removeUser('group-id', 'user-id');

// Get root groups (no parent)
$rootGroups = $authentik->groups()->getRootGroups();

// Get groups by parent
$childGroups = $authentik->groups()->getByParent('parent-group-id');

// Search groups
$results = $authentik->groups()->search('search-term');
```

### Provider Manager

```php
// List all providers
$providers = $authentik->providers()->all();

// Get OAuth2 providers
$oauthProviders = $authentik->providers()->getOAuth2();

// Get SAML providers
$samlProviders = $authentik->providers()->getSAML();

// Get Proxy providers
$proxyProviders = $authentik->providers()->getProxy();

// Create OAuth2 provider
$newProvider = $authentik->providers()->createOAuth2([
    'name' => 'My OAuth2 Provider',
    'client_type' => 'confidential',
    'authorization_flow' => 'flow-id'
]);

// Create SAML provider
$newProvider = $authentik->providers()->createSAML([
    'name' => 'My SAML Provider',
    'acs_url' => 'https://example.com/acs',
    'issuer' => 'my-issuer'
]);

// Search providers
$results = $authentik->providers()->search('search-term');
```

### Flow Manager

```php
// List all flows
$flows = $authentik->flows()->all();

// Get flow by slug
$flow = $authentik->flows()->getBySlug('default-authentication-flow');

// Get flows by designation
$authFlows = $authentik->flows()->getAuthenticationFlows();
$enrollmentFlows = $authentik->flows()->getEnrollmentFlows();
$recoveryFlows = $authentik->flows()->getRecoveryFlows();

// Execute flow
$result = $authentik->flows()->execute('flow-slug', [
    'context_data' => 'value'
]);

// Export flow
$exportData = $authentik->flows()->export('flow-id');

// Import flow
$authentik->flows()->import($exportData);

// Get flow diagram
$diagram = $authentik->flows()->getDiagram('flow-id');
```

### Source Manager

```php
// List all sources
$sources = $authentik->sources()->all();

// Get OAuth sources
$oauthSources = $authentik->sources()->getOAuthSources();

// Get SAML sources
$samlSources = $authentik->sources()->getSAMLSources();

// Get LDAP sources
$ldapSources = $authentik->sources()->getLDAPSources();

// Create OAuth source
$newSource = $authentik->sources()->createOAuthSource([
    'name' => 'GitHub OAuth',
    'slug' => 'github',
    'provider_type' => 'github'
]);

// Sync LDAP source
$authentik->sources()->syncLDAP('ldap-source-id');

// Get enabled/disabled sources
$enabledSources = $authentik->sources()->getEnabled();
$disabledSources = $authentik->sources()->getDisabled();
```

### Policy Manager

```php
// List all policies
$policies = $authentik->policies()->all();

// Get expression policies
$expressionPolicies = $authentik->policies()->getExpressionPolicies();

// Get group membership policies
$groupPolicies = $authentik->policies()->getGroupMembershipPolicies();

// Test policy
$result = $authentik->policies()->test('policy-id', [
    'user' => 'user-id',
    'http_request' => []
]);

// Create expression policy
$newPolicy = $authentik->policies()->createExpressionPolicy([
    'name' => 'Custom Policy',
    'expression' => 'return True'
]);
```

## Artisan Commands

### Sync Command

Sync data from Authentik to your local database:

```bash
# Sync all data
php artisan authentik:sync --all

# Sync users only
php artisan authentik:sync --users

# Sync groups only
php artisan authentik:sync --groups
```

## Error Handling

The SDK throws `AuthentikException` for API errors:

```php
use App\Services\Authentik\Exceptions\AuthentikException;

try {
    $user = $authentik->users()->get('non-existent-id');
} catch (AuthentikException $e) {
    Log::error('Authentik API Error: ' . $e->getMessage());
    // Handle the error appropriately
}
```

## Pagination

The SDK handles pagination automatically:

```php
// Get all results (auto-paginated)
$allUsers = $authentik->users()->all();

// Manual pagination
$page1 = $authentik->users()->list(['page' => 1, 'page_size' => 50]);
$page2 = $authentik->users()->list(['page' => 2, 'page_size' => 50]);
```

## Raw API Access

For endpoints not covered by the managers, use the raw client:

```php
// Custom API call
$response = $authentik->client()->get('/custom/endpoint', ['param' => 'value']);

// POST request
$response = $authentik->client()->post('/custom/endpoint', ['data' => 'value']);
```

## Security Considerations

1. **API Token Security**: Store your API token securely and never commit it to version control
2. **Permissions**: Ensure your API token has only the necessary permissions
3. **Rate Limiting**: Be mindful of API rate limits when making bulk operations
4. **Validation**: Always validate data before sending to Authentik
5. **Error Logging**: Log API errors for debugging and monitoring

## Example: Complete User Management

```php
// Create a complete user management example
class UserManagementService
{
    protected AuthentikSDK $authentik;

    public function __construct(AuthentikSDK $authentik)
    {
        $this->authentik = $authentik;
    }

    public function onboardUser(array $userData): array
    {
        // Create user
        $user = $this->authentik->users()->createWithPassword([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'name' => $userData['name'],
            'is_active' => true
        ], $userData['password']);

        // Add to default group
        $this->authentik->users()->addToGroup($user['pk'], 'default-group-id');

        // Send welcome email (if enabled in Authentik)
        $this->authentik->users()->sendRecoveryEmail($user['pk']);

        return $user;
    }

    public function offboardUser(string $userId): void
    {
        // Deactivate user
        $this->authentik->users()->deactivate($userId);

        // Terminate all sessions
        $this->authentik->users()->terminateSessions($userId);

        // Optionally remove from all groups
        $groups = $this->authentik->users()->getGroups($userId);
        foreach ($groups as $group) {
            $this->authentik->users()->removeFromGroup($userId, $group['pk']);
        }
    }
}
```

## Routes

The SDK includes pre-built routes for common operations. Access them at:

- `/authentik/applications` - List applications
- `/authentik/users` - List users
- `/authentik/users/search?q=term` - Search users
- `/authentik/groups` - List groups
- `/authentik/providers` - List providers
- `/authentik/flows` - List flows

## Contributing

When extending the SDK:

1. Follow the existing patterns in the managers
2. Add appropriate error handling
3. Document new methods
4. Include examples in the README

## License

This SDK is open-sourced software licensed under the MIT license.