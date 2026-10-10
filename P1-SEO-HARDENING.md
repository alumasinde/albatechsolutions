# P1 SEO and Content Trust Improvements

This phase builds on the P0 SEO/security hardening package.

## Changes

- Added explicit `published` dates to the guide catalogue; `updated` remains the last modification date.
- Updated guide article bylines to show prepared-by, publication date and update date when valid dates are present.
- Updated Article JSON-LD to use `published` for `datePublished` and `updated` for `dateModified`, rather than incorrectly treating the last update as the publication date.
- Sitemap generation now fails closed with HTTP 503 and `Cache-Control: no-store` if published service or portfolio URLs cannot be loaded. It no longer silently serves a partial sitemap that may be cached by crawlers.
- Hardened sitemap `lastmod` generation to avoid treating empty dates as valid timestamps.
- Expanded SEO regression coverage for the sitemap's unavailable-database behaviour.
- Added `tests/p1_rendered_seo_regression.php` to render Guides pages and check H1/title/description/canonical, robots indexability, image alt attributes, internal route patterns, JSON-LD structure, and Article publication/modification dates.

## Run checks

```bash
php tests/p1_rendered_seo_regression.php
php tests/seo_phase4_regression.php
php tests/p0_hardening_regression.php
```

The sitemap should be tested with the application's configured MySQL database as well. When database-backed published services or work projects cannot be loaded, `/sitemap.xml` intentionally returns HTTP 503 without caching an incomplete document. Review application logs for the underlying database error.

## Content trust notes

Publication dates in the static guide catalogue represent the content's declared publication date and should be kept accurate by editors. Do not claim independent review, professional qualifications, client results or official endorsement unless those facts are real and verifiable. Official-source links should be added to articles where they materially support time-sensitive government or regulatory guidance.
