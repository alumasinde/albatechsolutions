# Audit fixes (applied on top of the Phase 4 build)

## Bugs fixed
- **Wrong structured data**: home, /services and other listing pages emitted a `Service` JSON-LD node
  (a loop variable leaked into the layout). `Service` is now emitted only when a controller passes
  `schemaService` (ServiceController::show).
- **HEAD requests returned 405** (uptime monitors, link checkers, some crawlers). Router now answers HEAD
  through the matching GET route and discards the body.
- **Duplicate URLs**: `/services/` etc. now 301 to `/services` (strict allow-list; no open redirect).
- **Accessibility (axe-core)**: 7 pages failed colour contrast and the services page failed label-in-name.
  Fixed via tokens (`--color-text-muted/subtle`, `--color-success`, new `--color-accent-text`), the
  floating WhatsApp button (dark text on green + label starting with its visible text) and tile aria-labels.
  Result: 0 violations on 11 pages x mobile/desktop, with and without contact settings configured.
- **Contact page dead end**: with no WhatsApp/email set, the two cards told visitors to use each other.
  Now only configured channels render (plus a Phone card), one honest notice if none exist, filter chips only
  when there are 3+ cards, and a single card is centred.

## Improvements
- Organization JSON-LD now includes address / email / telephone **when configured** (nothing invented).
- Sitemap: real `<lastmod>` for guides and services.
- Favicon / apple-touch-icon use the real logo (was an off-brand inline icon, wrong blue).
- `tokens.css` is a normal `<link>` (was an `@import` = serial request chain); assets are versioned by
  mtime via `View::asset()`; `.htaccess` adds gzip + long-lived asset caching.

## Tests
`tests/audit_fixes_regression.php` (34 checks: contrast ratios, no @import, schema flag, HEAD, redirect
pattern safety, asset versioning). Whole suite: 103 checks passing.

## Needs YOUR input (not code)
Admin -> Settings: WhatsApp number, email, phone; confirm hours / response time / city (seed values are
published as facts); confirm `site_url` (www vs non-www must match the host redirect).
