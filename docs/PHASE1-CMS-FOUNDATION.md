# Phase 1: CMS foundation

This phase adds an admin-managed content layer to AlbaTech Solutions while keeping routing, authorization, input validation and rendering logic in PHP.

## Included

- Homepage content editor for the hero, image, homepage SEO title/description/social image, service and work headings, process steps, reasons to choose AlbaTech, work-area cards, service promise, bottom CTA and hero CTAs.
- Static page editor for About, Get Assistance, Contact & Location, Terms and Privacy pages. Pages support published/draft status, heading, intro, plain-text body, SEO title, meta description and social image.
- Database-managed header and footer navigation, including order, enabled state and optional new-tab behavior.
- Reusable CTA registry with unique keys, labels, destinations, placement and CSS classes. The homepage CTA editor synchronizes the two hero CTA records.
- One shared WhatsApp prefilled-message setting is used by the homepage and enquiry page.
- New immutable migration: `database/migrations/009_cms_foundation.sql`.
- Admin access uses the existing `settings.manage` permission, CSRF validation and existing audit log table.

## Apply locally (Windows / XAMPP)

1. Back up the local `albatechsolutions` database.
2. Ensure `.env` points to the correct MySQL database.
3. Install PHP dependencies if needed: `composer install`.
4. Run the new migration with the project's normal migration runner:

   ```powershell
   php bin/migrate.php
   ```

5. Sign in to `/admin` using an active administrator account.
6. Open **Pages & navigation** (`/admin/cms`).
7. Edit the Homepage, Static pages, Navigation and Reusable CTAs sections and save.
8. Preview the public pages and check page source for `<title>`, description, canonical and Open Graph image tags.

Do not edit migrations `000` through `008`; this phase adds a new migration instead.

## Content and security behavior

- Static page body content is plain text. Blank lines separate paragraphs; HTML and scripts are escaped and never executed.
- Links must be local paths or HTTPS URLs. Social/hero images must be local `/assets/` or `/uploads/` paths or HTTPS URLs.
- Draft CMS pages return a 404 response instead of silently rendering old hardcoded copy.
- The migration creates defaults with `INSERT IGNORE`/upsert-safe patterns so existing settings and navigation are preserved.
- Image upload management is not part of this phase; administrators may select an existing image path or provide an HTTPS image URL.

## Verification

Run PHP syntax checks on the project files. The migration and end-to-end admin/public behavior must also be verified against the project's MySQL database because static checks cannot confirm MySQL DDL, session/permission setup, or rendered pages against live records.

## CMS editor UI/UX update

The admin content editor now uses a focused Content Studio layout:

- Select Homepage, Website pages, Navigation, or Calls to action using responsive workspace cards.
- In Website pages, choose one static page from the page selector. Only that page's editor is shown at a time.
- Workspace selection is reflected in the URL hash, so the editor returns to the same section after save redirects.
- Form spacing, labels, focus states, save actions, and mobile layout have been refined in `public_html/assets/css/admin.css`.
- Interaction logic is in `public_html/assets/js/admin-cms.js` and does not require a JavaScript framework.

Verification after this UI update:

- `php -l resources/views/admin/cms.php`: passed.
- `php tests/cms_foundation_static_regression.php`: 24 checks passed.
- `php tests/guides_cms_regression.php`: 10 checks passed.
- `php tests/seo_phase4_regression.php`: still requires a running, migrated, seeded MySQL database; without MySQL it fails the published-guide-count assertion.
