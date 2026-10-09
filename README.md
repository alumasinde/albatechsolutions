# AlbaTech Solutions

Custom PHP + MySQL website foundation for AlbaTech Solutions.

## Requirements

- PHP 8.2+ with `pdo_mysql`, `mbstring`, and `openssl` enabled
- MySQL 8+ or a compatible local MySQL server
- Composer 2
- Apache with `mod_rewrite` (recommended for clean URLs), or PHP's local development server

## Setup on Windows

1. Create a local MySQL database:

   ```sql
   CREATE DATABASE albatechsolutions
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

2. Copy `.env.example` to `.env` and set your local database credentials.
3. Install Composer autoloading:

   ```powershell
   composer install
   ```
4. Run migrations:

   ```powershell
   php bin/migrate.php
   ```
5. Start the local server. The hosts entry must exist first:

   ```powershell
   composer serve
   ```

   Then open `http://albatechsolution.dev:8000`.

## Windows hosts file

Open Notepad as Administrator and edit:

`C:\Windows\System32\drivers\etc\hosts`

Add:

```text
127.0.0.1 albatechsolution.dev
```

If you prefer the default port 80 with Apache, configure a VirtualHost with `DocumentRoot` pointing to this project's `public` directory and use the same hosts entry. Do not expose the project root as the web root.

## Migrations

SQL files live in `database/migrations/` and use ordered, descriptive names such as `000_auth.sql`. The runner is `bin/migrate.php`. Successfully applied migrations are recorded in `schema_migrations`. Treat applied migrations as immutable; create a new numbered migration for changes.

## CSS convention

Styles are outside views in `public/assets/css/`. Design tokens are declared in `:root` in `tokens.css`; components consume those tokens. Avoid inline styles and page-specific CSS embedded in PHP views.

## Current scope

This is the foundation only: configuration, PDO, router, shared layout, a simple homepage, initial settings/auth schema, and migration runner. Admin CRUD, full authentication screens, service catalogue and SEO manager are subsequent build steps.
