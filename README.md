# AlbaTech Solutions

Lightweight, database-driven PHP website for AlbaTech Solutions.

## Local development (Windows)

Requirements: PHP 8.2+, Composer 2, MySQL 8+, `pdo_mysql`, and `mbstring`.

1. Create database `albatechsolutions` using `utf8mb4`.
2. Copy `.env.example` to `.env` and configure local DB credentials.
3. Run `composer install`.
4. Run migrations: `php bin/migrate.php`.
5. Add `127.0.0.1 albatechsolution.test` to the Windows hosts file.
6. Run `composer serve` and open `http://albatechsolution.test:8000/`.
php -S albatechsolution.test:8000 -t public

## Apache option

The document root must be the `public/` directory. Enable `mod_rewrite` and allow overrides for the `public/` directory. `.test` is the local development domain; use HTTPS on the real production domain.

## Migrations

Migration files use a three-digit prefix and descriptive name, e.g. `000_auth.sql`, `002_service_categories.sql`. The runner is `bin/migrate.php`. It records successful migrations and checks checksums to detect edits to applied files. MySQL DDL can auto-commit, so failed migrations may partially apply; inspect the database before retrying. Treat applied migrations as immutable.

## Architecture

- `app/Core`: environment loading, routing, PDO, request/response, view rendering.
- `app/Controllers`: HTTP controllers.
- `app/Repositories`: SQL queries.
- `resources/views`: PHP templates only; CSS belongs in `public/assets/css/`.
- `public/assets/css/tokens.css`: brand design tokens declared under `:root`.
- `routes/web.php`: public route definitions.

## Phase 2 service catalogue

The catalogue is read from MySQL through `ServiceRepository`. Categories and services are editable database records. Initial migration seed data creates a starter catalogue; the Admin Panel will be implemented separately. Only published services in active categories appear publicly.

Never collect customer passwords, PINs or M-Pesa credentials. Government services are independent assistance, not official government services.
