# AlbaTech Solutions V4.0 — Production Hardening & Launch

## Added

- `/healthz` liveness endpoint.
- `/readyz` readiness endpoint with database and production configuration checks.
- `bin/preflight.php` production deployment gate.
- `bin/backup.php` secure MySQL backup helper using a temporary 0600 client config.
- Migration `040_production_indexes.sql` for operational/growth query indexes.
- Strict PHP session mode and cookie settings.
- Additional security headers: COOP, CORP, Origin-Agent-Cluster and DNS prefetch control.
- HTTPS-only `upgrade-insecure-requests` CSP directive.
- Public upload directory server-side script execution protection.
- Production `.htaccess` blocking common temporary/source-map/VCS artifacts.
- `/.well-known/security.txt`.
- Canonical-host alignment for robots.txt, llms.txt, sitemap generation and Paystack callback configuration.
- V4 production, security and SEO runbooks.

## Production fail-closed controls

Production will not start when:

- `APP_DEBUG=true`.
- `APP_KEY` is missing or shorter than 32 characters.
- `APP_URL` is not HTTPS.

## Validation

- PHP syntax validation: passed with zero syntax errors.
- Final archive integrity: verified.
- Static production configuration and file-layout checks are available through `bin/preflight.php`.

## Important operational notes

- The application document root must be `public_html/` only.
- Run the preflight on the actual production server because database credentials, PHP extensions, writable paths and installed Composer dependencies are environment-specific.
- A backup is only considered reliable after a restoration test.
- V4.0 does not claim to replace an external penetration test, Cloudflare WAF tuning or CI dependency scanning; those remain part of ongoing operations.

## QA Hardening Pass

- Added `bin/qa.php` dependency-free QA runner.
- Added static duplicate PDO named-placeholder detection.
- Added Composer `qa` and `qa:sql` scripts.
- Added optional PHPStan and PHPUnit configuration.
- Added Growth analytics integration coverage.
- Fixed `GrowthAnalyticsRepository::servicePerformance()` native PDO HY093 caused by repeated `:days` placeholder.



php bin/preflight.php
php bin/qa.php
php bin/db-smoke.php


php -S 127.0.0.1:8000 -t public_html
