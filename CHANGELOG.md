# Changelog

All notable changes to the Portal project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Comprehensive README.md documentation
- Updated .env.example with all required configuration options
- MIT License file

## [1.0.0] - 2025-10-02

### Added
- Initial release of Portal application
- Authentik OAuth2/OIDC integration for authentication
- Custom AuthentikSDK for API communication
- User management with CRUD operations
- Group management with member assignment
- Application discovery from Authentik
- Flexible application access control system:
  - Default allow for applications with no policies
  - Restricted access for applications with assigned policies
- Portal Admin role-based authorization
- User dashboard showing accessible applications
- Admin dashboard with system statistics
- Email system with welcome and password recovery templates
- Responsive UI built with Tailwind CSS
- Comprehensive logging and error handling

### Features
- **Authentication**: OAuth2/OIDC via Authentik with secure session management
- **User Management**: Create, edit, delete users with group assignments
- **Group Management**: Full CRUD operations with superuser group support
- **Application Management**: Access control with group/user assignments
- **Dashboard**: User and admin dashboards with real-time statistics
- **Email System**: Customizable templates for user communications
- **Security**: CSRF protection, input validation, secure headers
- **API Integration**: Full Authentik REST API integration
- **Access Control**: Flexible application access policies

### Security
- OAuth2/OIDC authentication implementation
- CSRF protection on all forms
- Secure session handling with proper timeout
- Input validation and sanitization
- SQL injection prevention via Eloquent ORM
- XSS prevention through Blade templating

### Technical Stack
- Laravel 11.46 framework
- PHP 8.3+ compatibility
- MySQL/PostgreSQL database support
- Tailwind CSS for styling
- Vite for asset compilation
- Custom service architecture
- Modular SDK design

---

## Release Notes

### v1.0.0 - Initial Release

This is the first stable release of Portal, providing a complete management interface for Authentik identity provider. The application includes all core features needed for user, group, and application management with a focus on security and usability.

**Key Highlights:**
- Complete Authentik integration
- Flexible access control system
- Modern responsive UI
- Comprehensive admin tools
- Secure authentication flow

**Installation Requirements:**
- PHP 8.3+
- Authentik instance (self-hosted or cloud)
- MySQL or PostgreSQL database
- Web server (Nginx/Apache recommended)

**Breaking Changes:**
- None (initial release)

**Known Issues:**
- None reported

**Upgrade Notes:**
- None (initial release)