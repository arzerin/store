# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a CodeIgniter 4 PHP web application framework project. CodeIgniter 4 is a light, fast, flexible and secure full-stack PHP framework following the Model-View-Controller (MVC) pattern.

**Requirements:**
- PHP 8.1 or higher
- Required extensions: intl, mbstring, json
- Optional extensions based on features: curl, mysqli/pgsql/sqlite3, redis, etc.

## Development Commands

### Running the Application

```bash
# Start the built-in development server
php spark serve
```

The application entry point is `public/index.php`. Configure your web server to point to the `public/` directory.

### Database Operations

```bash
# Run migrations
php spark migrate

# Rollback last migration batch
php spark migrate:rollback

# Refresh database (rollback all + migrate)
php spark migrate:refresh

# Check migration status
php spark migrate:status

# Run database seeders
php spark db:seed [SeederName]

# Create a new database schema
php spark db:create
```

### Testing

```bash
# Install dependencies (includes PHPUnit)
composer install

# Run all tests
./vendor/bin/phpunit
# or on Windows: vendor\bin\phpunit

# Run tests for a specific directory
./vendor/bin/phpunit app/Models

# Run tests with coverage report
./vendor/bin/phpunit --colors --coverage-text=tests/coverage.txt --coverage-html=tests/coverage/ -d memory_limit=1024m
```

**Test Database Setup:** Configure the `tests` group in `app/Config/Database.php` or `.env` file before running database-related tests.

### Code Generation (Spark Generators)

```bash
# Generate new files using make: commands
php spark make:controller [ControllerName]
php spark make:model [ModelName]
php spark make:migration [MigrationName]
php spark make:seeder [SeederName]
php spark make:entity [EntityName]
php spark make:filter [FilterName]
php spark make:command [CommandName]
php spark make:validation [ValidationName]
php spark make:config [ConfigName]
php spark make:cell [CellName]
php spark make:test [TestName]
php spark make:scaffold [Name]  # Generates complete CRUD set
```

### Other Useful Commands

```bash
# List all available commands
php spark list

# Display all registered routes
php spark routes

# Check a specific route's filters
php spark filter:check [route]

# Clear system caches
php spark cache:clear

# Show cache information
php spark cache:info

# Verify namespace configuration
php spark namespaces

# Check configuration values
php spark config:check

# Generate encryption key
php spark key:generate

# Optimize for production
php spark optimize
```

### Dependency Management

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Run tests via composer
composer test
```

## Architecture Overview

### Directory Structure

- **app/** - Application code (your custom code)
  - **Config/** - Configuration files (database, routes, app settings, filters, etc.)
  - **Controllers/** - Controllers handle HTTP requests and responses
  - **Models/** - Models interact with the database (optional, but recommended for data access)
  - **Views/** - View templates for rendering HTML
  - **Database/** - Migrations and seeders
  - **Filters/** - Request/response filters (authentication, CORS, etc.)
  - **Helpers/** - Custom helper functions
  - **Libraries/** - Custom library classes
  - **Language/** - Localization files
  - **ThirdParty/** - Third-party libraries not managed by Composer

- **system/** - CodeIgniter 4 framework core (do not modify)
- **public/** - Web server document root, contains `index.php` entry point
- **writable/** - Logs, cache, sessions, and uploads (must be writable)
- **tests/** - Application test files

### MVC Pattern

**Routes** (`app/Config/Routes.php`) → **Controllers** (`app/Controllers/`) → **Models** (`app/Models/`) → **Views** (`app/Views/`)

1. Routes map URLs to controller methods
2. Controllers process requests, interact with models, and return views or JSON
3. Models handle database operations and business logic
4. Views render the output (HTML, JSON, etc.)

### Configuration

- Primary config: `app/Config/` directory contains all configuration classes
- Environment-specific: Copy `env` to `.env` and customize settings
- Database: Configure in `app/Config/Database.php` or via `.env` variables
- Routes: Define in `app/Config/Routes.php`
- Filters: Configure request/response filters in `app/Config/Filters.php`
- Autoloading: PSR-4 autoloading configured in `app/Config/Autoload.php` and `composer.json`

### Key CodeIgniter Concepts

**Service Layer:** CodeIgniter uses a Services pattern for accessing framework components. Services are configured in `app/Config/Services.php` and can be accessed via `service('name')` or `\Config\Services::name()`.

**Helpers and Libraries:** Helpers are collections of functions (loaded via `helper('name')`). Libraries are classes for reusable functionality.

**Request/Response:** Use `$this->request` in controllers for input data. Return strings, views, or Response objects from controller methods.

**Validation:** Define validation rules in `app/Config/Validation.php` or inline in controllers/models.

**Filters:** Middleware-like filters run before/after controllers (CSRF, authentication, CORS, etc.). Configure in `app/Config/Filters.php` and `app/Config/Routes.php`.

**Database Query Builder:** Use the Query Builder for database operations: `$this->db->table('tablename')->...`

**Migrations:** Version control for database schema. Create with `php spark make:migration`, run with `php spark migrate`.

## Testing Notes

- Tests extend `CodeIgniter\Test\CIUnitTestCase`
- Test method names must start with `test`
- Database tests require proper test database configuration
- PHPUnit configuration in `phpunit.xml.dist` (copy to `phpunit.xml` for customization)
- XDebug with `xdebug.mode=coverage` required for code coverage reports

## Environment Configuration

Create a `.env` file from the `env` template to configure:
- `CI_ENVIRONMENT` (development/production/testing)
- `app.baseURL`
- Database credentials
- Encryption keys
- Session configuration
- Logger settings
