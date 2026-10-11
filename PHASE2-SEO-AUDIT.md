# AlbaTech Solutions — Phase 2 SEO audit

## Changes included

- Centralized title, description and social-image fallback behavior in `app/Core/SeoDefaults.php`.
- Added editable `default_meta_title` and `default_social_image` settings and migration `010_seo_defaults.sql`.
- Ensured local/staging `APP_URL` takes precedence over the production database `site_url` when `APP_ENV` is not `production`, preventing local canonical tags from pointing at production.
- Centralized WhatsApp destination and configured prefill-message behavior in `app/Core/WhatsAppLink.php`; homepage, global floating action, portfolio detail and contact-details page now use the shared builder. The Get Assistance form still composes a context-specific message from submitted form fields.
- Sitemap now lists only published CMS pages that map to actual public routes, includes their `lastmod`, and no longer blindly lists standard CMS pages when they are drafts.
- `llms.txt` now uses published CMS page titles and introductions for supported public CMS routes.
- Removed empty `datePublished`/`dateModified` values from Article JSON-LD and removed incorrect fixed PNG/dimensions claims for arbitrary social images.
- Added fast regression tests and a seeded-MySQL integration test.

## Apply on Windows/XAMPP

1. Back up the project and MySQL database.
2. Merge this project update into the current Phase 1 CMS UI/UX codebase, preserving `.env` and any local changes.
3. Ensure `.env` has `APP_ENV=local` and `APP_URL=http://albatechsolution.test:8000` (use the actual local origin).
4. Run `composer install` if `vendor/` is absent.
5. Start MySQL in XAMPP and confirm `.env` database credentials.
6. Run `php bin/migrate.php` to apply `010_seo_defaults.sql`.
7. Seed guides using the existing idempotent seed file, if needed: `database/seeds/004_seed_guides.sql`.
8. Run fast regression tests: `php tests/seo_phase2_regression.php` and the existing test suite.
9. Run the database-backed checks: `php tests/seo_phase2_mysql_integration.php`.

The integration test intentionally fails if MySQL, required migrations, or seeds are missing. It checks the actual sitemap response and rendered CMS metadata/JSON-LD against the configured database.

## Verification note

This build environment has PHP CLI but no `pdo_mysql` extension and no reachable MySQL server, so a real seeded-MySQL integration run cannot be performed here. Static/unit regression tests and PHP syntax checks are run before packaging; the seeded-MySQL test is included for execution on the user's XAMPP environment and must pass before production deployment.
