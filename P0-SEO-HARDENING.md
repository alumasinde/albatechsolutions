# P0 SEO and security hardening

This patch addresses the P0 findings identified in the source review.

## Changes

- Removed the layout fallback that incorrectly canonicalized any indexable page with a missing canonical URL to the homepage. Indexable page controllers must now provide their own absolute canonical URL; absent canonicals are omitted instead of emitting a wrong one.
- Added permanent lowercase redirects for safe ASCII GET/HEAD URL paths while preserving query strings. Existing trailing-slash redirects remain in place. Non-GET requests are not redirected by this normalization.
- Changed `/contact` and `/contact-details` to permanent redirects to `/get-help`, avoiding duplicate contact content.
- Removed the redirected `/contact-details` URL from the XML sitemap and `llms.txt` page list; added/retained the canonical `/get-help` entry in the sitemap.
- Escaped the 500 error template's displayed message and changed exception handling to pass raw message text to the escaping template, avoiding both HTML injection and double escaping.
- Removed duplicate `class` attributes from the 403 template. The 403, 404 and 500 response status codes continue to be set by the response/exception handling layer before output.
- Added `tests/p0_hardening_regression.php` and updated the Phase 4 SEO test to assert sitemap canonical URL behaviour.

## Validation performed

- PHP syntax check: all PHP files under `app`, `routes`, `resources/views`, `public`, `tests`, and `bin` passed.
- `tests/p0_hardening_regression.php`: 14 checks passed.
- `tests/security_baseline.php`: 5 checks passed.
- `tests/security_phase1_regression.php`: 11 checks passed.
- `tests/contact_delivery_regression.php`: 11 checks passed.
- `tests/work_portfolio_regression.php`: 10 checks passed.
- `tests/seo_phase4_regression.php`: 44 checks passed. Dynamic service/project sitemap database queries logged expected connection errors because MySQL was not available in this validation environment; test the sitemap again with the site's configured database before deployment.

## Deployment notes

1. Back up the existing project and database.
2. Merge these project files while preserving the production `.env`, uploads, and database.
3. Confirm `APP_URL` and the admin-configured `site_url` use the preferred production HTTPS origin.
4. Run the P0 and SEO regression tests with the production-like environment and a working MySQL database.
5. Verify `/contact` and `/contact-details` return HTTP 301 to `/get-help`, uppercase URL variants redirect to lowercase, trailing-slash variants redirect to slashless paths, and unknown URLs return HTTP 404.
6. Crawl the staging site and confirm each indexable page emits the correct canonical. The layout intentionally no longer invents a homepage canonical when a controller omits one.
