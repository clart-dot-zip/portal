# Portal - Authentik Integration Dashboard

A Laravel-based management portal for Authentik identity provider with comprehensive user, group, and application management capabilities.

![Laravel](https://img.shields.io/badge/Laravel-11.46-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue?style=flat-square&logo=php)
![Authentik](https://img.shields.io/badge/Authentik-Compatible-orange?style=flat-square)

## 🌟 Features

### 🔐 Authentication & Authorization
- **OAuth2/OIDC Integration** with Authentik
- **Role-based Access Control** with Portal Admin privileges
- **Secure Session Management** with automatic logout
- **User Profile Management** with group assignments

### 👥 User Management
- **Complete CRUD Operations** for users
- **Group Assignment** and management
- **Portal Admin** role assignment/removal
- **User Search & Filtering** capabilities
- **Bulk Operations** support

### 👨‍👩‍👧‍👦 Group Management
- **Create/Edit/Delete** groups in Authentik
- **Superuser Group** creation and management
- **Member Management** - add/remove users from groups
- **Group Statistics** and member counts

### 🏢 Application Management
- **Application Discovery** from Authentik
- **Access Control Management** with flexible policies:
  - **Default Allow**: Applications with no access policies are accessible to everyone
  - **Restricted Access**: Applications with policies restrict access to assigned users/groups
- **Group-based Access Assignment**
- **Direct User Access** assignment (where supported)
- **Application Statistics** and usage metrics
- **Launch URL Management**

### 📊 Dashboard & Analytics
- **User Dashboard** showing accessible applications
- **Admin Dashboard** with system-wide statistics
- **Real-time Metrics** for users, groups, and applications
- **Activity Monitoring** and recent login tracking

### 📧 Email System
- **Welcome Emails** for new users
- **Password Recovery** notifications
- **Responsive Email Templates** with clean design
- **Customizable Branding** and styling

## 🚀 Installation

### Prerequisites

- **PHP 8.3+** with required extensions
- **Composer** for dependency management
- **Node.js & NPM** for asset compilation
- **MySQL/PostgreSQL** database
- **Authentik Instance** (self-hosted or cloud)

### 1. Clone Repository

```bash
git clone https://github.com/clart-dot-zip/portal.git
cd portal
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Build assets
npm run build
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` file with your configuration:

```env
# Application Settings
APP_NAME=Portal
APP_URL=https://your-portal-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=portal
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Authentik OAuth Configuration
AUTHENTIK_CLIENT_ID=your_authentik_client_id
AUTHENTIK_CLIENT_SECRET=your_authentik_client_secret
AUTHENTIK_REDIRECT_URI=https://your-portal-domain.com/auth/callback
AUTHENTIK_BASE_URL=https://your-authentik-instance.com

# Authentik API Configuration
AUTHENTIK_API_TOKEN=your_authentik_api_token

# Email Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate

# (Optional) Seed test data
php artisan db:seed
```

### 6. Configure Authentik

#### Create OAuth Application in Authentik:

1. Go to **Applications** → **Applications** in Authentik admin
2. Click **Create**
3. Configure the application:
   - **Name**: Portal
   - **Slug**: portal
   - **Provider**: Create new OAuth2/OpenID Provider
   - **Launch URL**: `https://your-portal-domain.com`

#### Create OAuth Provider:

1. **Authorization flow**: Select your authorization flow
2. **Client ID**: Copy this to `AUTHENTIK_CLIENT_ID`
3. **Client Secret**: Copy this to `AUTHENTIK_CLIENT_SECRET`
4. **Redirect URIs**: Add `https://your-portal-domain.com/auth/callback`
5. **Scopes**: Include `openid`, `profile`, `email`

#### Create API Token:

1. Go to **Tokens & App passwords** in Authentik
2. Create new token with appropriate permissions
3. Copy token to `AUTHENTIK_API_TOKEN`

### 7. Start Application

```bash
# Development server
php artisan serve

# Production (with web server like Nginx/Apache)
# Point document root to /public directory
```

## 🔧 Configuration

### Portal Admin Setup

The first user to login will need to be manually assigned Portal Admin privileges:

1. Login to the portal with your Authentik account
2. Access Authentik admin panel
3. Create a group called "Portal Admin" 
4. Add your user to this group
5. Refresh the portal - you'll now have admin access

### Access Control Configuration

The portal supports flexible application access control:

#### Default Allow (Public Access)
- Applications with **no access policies** are accessible to all users
- Great for internal tools that everyone should access

#### Restricted Access
- Applications with **assigned groups/users** restrict access to only those entities
- Perfect for sensitive applications requiring specific permissions

To configure access:
1. Go to **Applications** → Select application → **Manage Access**
2. Assign groups or users as needed
3. Access status updates automatically

## 📖 Usage

### User Dashboard
- View accessible applications
- Launch applications directly
- Manage personal profile
- View group memberships

### Admin Functions

#### User Management
- Create new users
- Edit user profiles
- Assign/remove from groups
- Grant/revoke Portal Admin access
- Search and filter users

#### Group Management
- Create custom groups
- Manage group memberships
- Set superuser permissions
- Monitor group statistics

#### Application Management
- View all applications from Authentik
- Configure access policies
- Monitor application usage
- Manage launch URLs and metadata

## 🛡️ Security Features

- **OAuth2/OIDC** authentication via Authentik
- **CSRF Protection** on all forms
- **Session Security** with proper timeout handling
- **Input Validation** and sanitization
- **Secure Headers** and cookie settings
- **SQL Injection** protection via Eloquent ORM
- **XSS Prevention** through Blade templating

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
```

## 🔍 Troubleshooting

### Common Issues

#### Authentication Loops
- Verify Authentik OAuth configuration
- Check redirect URIs match exactly
- Ensure client ID/secret are correct

#### API Connection Errors
- Verify API token has proper permissions
- Check Authentik base URL is accessible
- Review firewall/network restrictions

#### Database Connection Issues
- Verify database credentials
- Ensure database exists and is accessible
- Check PHP extensions (mysql/pgsql)

### Logs

```bash
# View application logs
tail -f storage/logs/laravel.log

# Clear logs
php artisan log:clear

# View specific log level
grep ERROR storage/logs/laravel.log
```

### Cache Issues

```bash
# Clear all caches
php artisan optimize:clear

# Or individually:
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database
- [ ] Set up SSL certificates
- [ ] Configure email delivery
- [ ] Set up backup strategy
- [ ] Configure monitoring
- [ ] Set appropriate file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up cron for scheduled tasks

### Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name your-portal-domain.com;
    root /path/to/portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Scheduled Tasks

The portal includes automated Authentik synchronization via Laravel's scheduler:

#### Configured Schedule

- **Authentik Full Sync**: `php artisan authentik:sync --all` - Runs hourly
  - Syncs all users, groups, applications, and related data
  - Prevents overlapping executions  
  - Runs in background
  - Logs to `storage/logs/authentik-sync.log`

#### Setup Cron Job

Add to crontab for scheduled operations:

```bash
* * * * * cd /path/to/portal && php artisan schedule:run >> /dev/null 2>&1
```

#### Manual Sync Commands

```bash
# Test Authentik connection
php artisan authentik:test

# Sync all data from Authentik
php artisan authentik:sync --all

# Sync only users
php artisan authentik:sync --users

# Sync only groups  
php artisan authentik:sync --groups

# View scheduled commands
php artisan schedule:list

# Run scheduler manually (for testing)
php artisan schedule:run
```

#### Sync Logs

All sync operations are logged to `storage/logs/authentik-sync.log` for monitoring and troubleshooting.

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for changes
- Use meaningful commit messages
- Ensure backwards compatibility

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [Wiki](https://github.com/clart-dot-zip/portal/wiki)
- **Issues**: [GitHub Issues](https://github.com/clart-dot-zip/portal/issues)
- **Discussions**: [GitHub Discussions](https://github.com/clart-dot-zip/portal/discussions)

## 🏗️ Architecture

### Technology Stack

- **Backend**: Laravel 11.46 (PHP 8.3+)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: MySQL/PostgreSQL
- **Authentication**: Authentik OAuth2/OIDC
- **API**: Authentik REST API integration
- **Build Tools**: Vite for asset compilation

### Key Components

- **AuthentikSDK**: Custom SDK for Authentik API integration
- **ApplicationAccessService**: Handles application access control logic
- **Middleware**: Portal admin authentication and authorization
- **Services**: Modular service classes for business logic
- **Controllers**: RESTful controllers for web interface

---