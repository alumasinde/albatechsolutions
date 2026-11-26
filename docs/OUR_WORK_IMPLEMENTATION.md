# Our Work portfolio implementation

## Included
- Admin → Our Work: add, edit, publish, archive, feature, reorder, and delete projects.
- Project fields: title, unique slug, active service category, description, optional client name, optional HTTP(S) live link, cover image, SEO title/description, draft/published/archived status, featured flag, sort order.
- Public `/work` grid with service-category filter query (`?category=slug`) and public `/work/{slug}` detail pages.
- Featured homepage carousel with dots and pause/play. Autoplay pauses on hover, focus, touch/pointer activity and hidden tabs; respects reduced-motion preference.
- Related work on project and service detail pages.
- WhatsApp request CTA uses the site's configured WhatsApp number and names the project in its prefilled message. If WhatsApp is not configured, it falls back to a project-specific enquiry URL.
- Public published project URLs are included in sitemap.xml and `/work` is listed in llms.txt.
- Upload checks: actual uploaded file, 8 MB maximum, raster MIME/dimension checks, random filename, no SVG; when GD WebP is available images are resized to 1800px maximum edge and converted to WebP. Apache upload directory blocks script-like extensions.
- Admin actions require `work.manage` permission and CSRF validation.

## Apply
1. Back up the database and current project folder.
2. Merge/replace the project files from this archive, preserving your real `.env` and any local changes.
3. From the project root, run `composer install` if needed.
4. Ensure `.env` points to the intended MySQL database, then run `php bin/migrate.php`. This applies the new `007_work_projects.sql` migration; previous migrations are unchanged.
5. Sign in at `/admin`, open **Our Work**, and add a test project with a real screenshot. Publish and feature it to check the homepage carousel.
6. Verify `/work`, a project detail page, service-related work, `/sitemap.xml`, and mobile layout.

## Notes
- Only publish client names, screenshots and live links when the client has approved public display. An anonymised client name is optional.
- Cover images require writable `public_html/uploads/work` storage. WebP conversion requires PHP GD with WebP support; without it, validated JPG/PNG/WebP uploads are stored as received.
- Database-backed routes have not been live-tested against your MySQL instance in this environment.
