# P2 : Performance and User Experience Hardening

This phase continues the P0/P1 SEO-hardened AlbaTech Solutions PHP application. It focuses on practical frontend performance without changing the application's database schema or public URL structure.

## Changes

- Static CSS, JavaScript, and image assets receive a one-year `Cache-Control: public, max-age=31536000, immutable` policy. This is paired with `View::asset()`'s file-modification-time query versioning so updated files receive a new URL.
- Added one-year expiry policies for JPEG, AVIF and other common static image formats.
- Preloads the homepage hero image when the canonical URL identifies the homepage, and sets high fetch priority on that image.
- The homepage hero image uses eager loading, async decoding and explicit width/height to help prevent layout shifts.
- Preserved existing lazy loading for portfolio and related-work thumbnails, with CSS aspect ratios reserving space while images load.
- Added connection hints and a consistent cross-origin/referrer policy for the admin guest Font Awesome stylesheet.
- Added `tests/p2_performance_regression.php` to guard these performance choices.

## Validation

Run from the project root:

```bash
php tests/p2_performance_regression.php
php tests/p0_hardening_regression.php
php tests/p1_rendered_seo_regression.php
php tests/seo_phase4_regression.php
```

Run all existing test scripts with the configured database enabled. The performance regression test is source-level and does not replace a live Lighthouse/PageSpeed or real-device test.

## Deployment notes

- Apache needs `mod_expires`, `mod_headers`, and `mod_deflate` enabled for all configured directives to apply. If your host uses Nginx, translate the cache/compression rules into its server configuration.
- Because assets are cached for one year, deploy changed files with the updated `View::asset()` version query. Do not remove query versioning while retaining immutable caching.
- Confirm hero image dimensions and image weight against the real deployment. This phase does not convert arbitrary uploads; uploaded project images should be resized/compressed in the upload pipeline as a separate enhancement.
- Test mobile navigation, carousel interaction, scroll behaviour and the contact form on a real mobile browser. No live browser, MySQL, Lighthouse or Core Web Vitals run was available in the build environment.
