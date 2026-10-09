# AlbaTech Solutions

Lightweight, database-driven PHP website for AlbaTech Solutions.

## Local development (Windows)

Requirements: PHP 8.2+, Composer 2, MySQL 8+, `pdo_mysql`, and `mbstring`.

1. Create database `albatechsolutions` using `utf8mb4`.
2. Copy `.env.example` to `.env` and configure local DB credentials.
3. Run `composer install`.
4. Run migrations: `php bin/migrate.php`.
5. Add `127.0.0.1 albatechsolution.test` to the Windows hosts file.
6. Run `composer serve` and open `http://albatechsolution.test:8000/`. The serve script disables Composer's process timeout because the PHP development server is intended to run continuously. You can also run `php -S albatechsolution.test:8000 -t public` directly.

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

The catalogue and homepage featured-services section are read from MySQL through `ServiceRepository`. Categories and services are database records. Only published services in active categories appear publicly. The `is_featured` flag controls homepage promotion. Service pricing uses `pricing_type`, `pricing_amount`, and `pricing_label`: use `fixed` for a set price, `starting_at` for a starting price, or `quote` to display the contact-for-price label. Prices are rendered from database values; do not hardcode them in views. The current starter records use contact-for-price until you set real prices. The Admin Panel for managing these fields and service visibility is a separate next phase.

Never collect customer passwords, PINs or M-Pesa credentials. Government services are independent assistance, not official government services.

## Admin Panel

After running `php bin/migrate.php`, create the first administrator from PowerShell in the project root:

```powershell
php bin/create-admin.php admin@yourdomain.co.ke
```

Enter the administrator's first name, last name and a unique password of at least 12 characters when prompted. Then visit `http://albatechsolution.test:8000/admin/login`.

The Admin Panel includes:

- Overview of service, publication, featured-service and category counts.
- Service creation and editing, category assignment, descriptions, SEO title/description, ordering and publication status.
- Separate homepage-feature toggle. A featured service is only displayed publicly while it is published and its category is active.
- Database-driven pricing choices: fixed price, starting price, or contact for price.
- Category creation and editing, including active/inactive state.
- Website settings for business name, website URL, contact details, WhatsApp number and default SEO metadata.
- CSRF checks on admin forms, password hashing, session regeneration on login, secure session cookie settings, and admin audit events.

Admin routes are protected by the login session. Keep `.env`, database credentials and backups private; use HTTPS and a strong unique password in production. Font Awesome and Google Fonts are loaded from their public CDNs in the admin interface.


## Security baseline and production checklist

The application includes baseline admin security controls: CSRF validation on admin mutations, session ID regeneration after login, idle/absolute session expiry, secure/HttpOnly/SameSite session cookies, file-backed login throttling, role-permission checks on service/category/settings screens, scalar-only request inputs, server-side length and format validation, audit logging, browser security headers, and production configuration checks.

### Local development

- Keep `APP_ENV=local` and `APP_DEBUG=true` only on your local machine.
- Use a unique, strong admin password (12+ characters). Create an admin account with `php bin/create-admin.php admin@example.com`.
- Rate-limit state is stored under `storage/cache/rate-limits`; make this directory writable by PHP and keep it outside the public web root.
- Login throttling uses `REMOTE_ADDR`; forwarded client-IP headers are intentionally not trusted by default. Configure a trusted-proxy strategy before placing the application behind a reverse proxy/load balancer.

### Before production

1. Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL=https://your-real-domain`. The application refuses requests if production debug is enabled, the configured URL is not HTTPS, or the database username is `root`.
2. Set `SESSION_SECURE_COOKIE=true`; use HTTPS across the entire site. HSTS is emitted on HTTPS requests.
3. Create a dedicated MySQL user with only the privileges this application requires; never use the database root account.
4. Configure the web server document root to the `public/` directory. Do not expose the project root, `.env`, `storage/`, `vendor/`, database files, or logs.
5. Keep `.env` out of version control and set restrictive file permissions on `.env`, logs, and storage directories.
6. Test administrator login, failed-login throttling, role permissions, CSRF failures, session expiry, logout, service/category/settings validation and recovery from database errors against the actual production-like environment.
7. Back up the database and verify that a restore works before launch. Keep PHP and Composer dependencies patched.

The headers currently allow inline scripts/styles because the existing service editor uses inline JavaScript and the templates include inline styling. For stronger CSP, move inline code to local assets or adopt per-response nonces and then remove `unsafe-inline` after browser testing. This baseline is not a substitute for a live penetration test.

### Run local security baseline checks

```powershell
php tests/security_baseline.php
```

These checks cover scalar request handling, path normalization, and login limiter thresholds. They do not replace integration tests against MySQL or a penetration test.
