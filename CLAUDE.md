# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Event management system built with PHP using Laravel Illuminate components (database, events, http), featuring a custom router and layered architecture. Uses Docker/FrankenPHP for serving, MySQL for persistence, and Phinx for migrations.

## Development Commands

### Docker & Server
```bash
# Start services (server on port 8080, MySQL on 3306)
docker-compose up -d

# Rebuild and restart after Dockerfile changes
docker-compose up -d --build

# Stop services
docker-compose down

# View logs
docker-compose logs -f server

# Access PHP container
docker-compose exec server bash

# Run commands inside container
docker-compose exec server php <command>
```

### Database & Migrations
```bash
# Run migrations (inside container)
docker-compose exec server php vendor/bin/phinx migrate

# Check migration status
docker-compose exec server php vendor/bin/phinx status

# Rollback last migration
docker-compose exec server php vendor/bin/phinx rollback

# Create new migration
docker-compose exec server php vendor/bin/phinx create MigrationName

# Run seeders
docker-compose exec server php vendor/bin/phinx seed:run
```

### Code Quality
```bash
# Run PHP CS Fixer (inside container)
docker-compose exec server php vendor/bin/php-cs-fixer fix
```

### Composer
```bash
# Install dependencies (inside container)
docker-compose exec server composer install

# Update dependencies
docker-compose exec server composer update

# Dump autoload
docker-compose exec server composer dump-autoload
```

## Architecture

### Layer Responsibilities

**Model** (`src/Models/`)
- Eloquent models extending `Illuminate\Database\Eloquent\Model`
- Define table structure, casts, relationships (belongsToMany, hasMany)
- Domain-specific methods (e.g., `isUpcoming()`, `canBeBooked()`)
- Example: `Attendee::class`, `Event::class`

**Repository** (`src/Repositories/`)
- Database query layer - all database access goes here
- Methods like `findAll()`, `findById()`, `create()`, `update()`
- Wrap Eloquent queries and return Models or Collections
- Example: `AttendeeRepository::findWithEvents()`

**Service** (`src/Services/`)
- Business logic and workflows
- Coordinate between multiple repositories
- Validation, error handling, complex operations
- Example: `AttendeeService::registerForEvent()` handles registration workflow

**Controller** (`src/Controllers/`)
- HTTP request/response handling
- Parse request, call service, return JSON response
- Use DTOs for type-safe request mapping
- Example: `AttendeeController::getAllAttendees()`

**DTO** (`src/DTOs/`)
- Data Transfer Objects for type-safe request/response handling
- Map JSON to strongly-typed PHP objects using `brick/json-mapper`
- Transform data between layers with `toArray()`, `toDatabaseArray()`

**Helpers** (`src/Helpers/`)
- Utility functions (Response formatting, Exception handling)

### Request Flow
```
1. Request → router.php (custom Router class)
2. Router → Controller method (via RouteItem)
3. Controller → Parses RequestItem, maps to DTO
4. Controller → Service (business logic)
5. Service → Repository (database access)
6. Repository → Eloquent Model → Database
7. Response ← Controller (JSON via Response::sendSuccess())
```

## Key Files

### Entry Point
- `public/index.php` - Application entry point, defines routes, bootstraps database connection

### Core Infrastructure
- `router.php` - Custom routing system (Router, RouteItem, RequestItem classes)
- `database/connection.php` - Eloquent setup, SQL query logging to Ray with EXPLAIN ANALYZE
- `phinx.php` - Phinx migration configuration
- `composer.json` - Dependencies (PSR-4 autoloading: `App\` → `src/`)

### Database
- `database/migrations/` - Phinx migration files (timestamped PHP classes)
- `database/seeders/` - Phinx seeder files
- `database/utils/SqlHighlighter.php` - SQL syntax highlighting for Ray
- `database/utils/ExplainAnalyzer.php` - Parse EXPLAIN ANALYZE output

### Testing
- `bruno/` - Bruno API collection for testing endpoints

## Database Configuration

Connection configured in `database/connection.php` using Illuminate Database Capsule:
- Host/credentials from `.env` or `.env.local` (local takes precedence)
- Uses `vlucas/phpdotenv` for environment variables
- Eloquent ORM enabled globally via `$capsule->setAsGlobal()`
- Query logging to Ray with syntax highlighting and performance analysis

## Routing System

Custom router in `router.php`:
- Pattern-based routing with dynamic parameters (`:id`, `:slug`)
- Supports GET, POST, PUT, DELETE
- Controller format: `[ClassName::class, 'methodName']`
- URL parameters extracted to `$request->params` array
- Example: `->get('/api/attendee/get/:id', [AttendeeController::class, 'getAttendeeById'])`

## Important Patterns

### Creating New Endpoints
1. Define route in `public/index.php`
2. Create/update Controller method to handle RequestItem
3. Create DTO for request body mapping if needed
4. Implement business logic in Service
5. Add Repository methods for database operations
6. Use Response::sendSuccess() or ExceptionHandler::handle()

### Working with Models
- Use Eloquent relationships defined in Models
- Repository methods should return Models or Collections
- Services operate on Models returned from Repositories
- Controllers serialize Models to JSON via `toArray()`

### Error Handling
- All controller methods wrapped in try/catch
- Exceptions handled by `ExceptionHandler::handle($e)`
- Business validation throws `InvalidArgumentException`, `RuntimeException`
- Response helper formats success/error JSON

### Debugging
- Ray debugging enabled (`spatie/ray`)
- SQL queries auto-logged with EXPLAIN ANALYZE performance data
- Color-coded by performance: green (fast), yellow (moderate), red (slow)
- Use `ray()` anywhere for debugging

## Stack Details

- **Server**: FrankenPHP (modern PHP app server with worker mode)
- **Database**: MySQL 8+ (via docker-compose)
- **ORM**: Laravel Eloquent (Illuminate Database)
- **Router**: Custom implementation (router.php)
- **Migrations**: Phinx
- **Debugging**: Spatie Ray
- **DTO Mapping**: brick/json-mapper
- **Code Standards**: PHP-CS-Fixer (friendsofphp/php-cs-fixer)
