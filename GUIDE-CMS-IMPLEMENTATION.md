# Guides CMS implementation

## Features
- Admin navigation: **Guides & Articles**
- Create, edit, draft, publish, archive and delete guides.
- Rich-text editor with paragraph and H1-H6 formats, bold, italic, underline, strike-through, alignment, links, image URLs, tables, lists, indentation, undo and redo.
- Sticky formatting toolbar remains visible while scrolling the editor page.
- Server-side HTML allow-list sanitizer before storing article content.
- Published database guides appear on `/guides` and `/guides/{slug}`; database records override built-in guides with the same slug. Draft/archived records with matching slugs suppress the built-in article from public display.
- SEO title, description, summary, category and URL slug fields.

## Install
1. Back up files and MySQL.
2. Merge this ZIP into the current project while preserving `.env` and uploads.
3. Run `composer install` if needed.
4. Run `php bin/migrate.php` with MySQL configured to apply `008_guides_cms.sql`.
5. Sign in and open `/admin/guides`.

## Notes
- Admin access uses the existing `services.manage` permission to align with the current admin role model.
- Images are inserted by public HTTPS URL or a site-relative URL. This version does not upload image files from the editor; store images in the site's managed uploads folder first.
- HTML is sanitized server-side. Inline style attributes are restricted, scripts and embedded content are removed, and link/image URLs are allow-listed.
- Use the article title as the page H1. Use H2/H3/H4 for sections and subsections inside the article body.
- Verify public content and database migration in staging before production.
