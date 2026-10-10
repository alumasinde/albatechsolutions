# Phase 4 — SEO and Content

## Included

- Shared SEO metadata layout with page-specific title, meta description, canonical URL, robots directives, Open Graph and Twitter card metadata.
- Absolute social-preview image URLs, a 1200×630 PNG sharing card, and a separate square logo for Organization structured data.
- Organization and WebSite JSON-LD across public pages, Service schema on service detail pages, and Article schema on guide detail pages.
- Canonical base URL resolution from the public `site_url` setting with `APP_URL` fallback.
- Updated dynamic sitemap containing public core pages, legal/contact pages, published service slugs and the guide articles.
- `robots.txt` sitemap reference and admin/health disallow directives; `/llms.txt` includes guides and legal/contact resources.
- Five useful guides: website design costs in Kenya, domain vs hosting, business website preparation, local online visibility and business registration preparation.
- Individual clean guide URLs, responsive editorial cards, breadcrumbs, related links and enquiry calls to action.
- Restored Terms of Service, Privacy Policy and Contact & Location routes and footer links in this source baseline.
- Updated public copy to avoid advertising IT support as a service.

## Local verification performed

- PHP syntax lint: all PHP files passed.
- Security baseline: 5 checks passed.
- Admin workflow baseline: 10 checks passed.
- Phase 1 security regression: 11 checks passed.
- Phase 4 SEO/content regression: 43 checks passed.
- CSS token reference scan: all 73 referenced custom properties have definitions in `tokens.css`.

## Still required before production

- Run `composer install`, configure `.env`, and run the website against the actual MySQL database.
- Confirm Admin → Settings has `site_url=https://albatechsolutions.co.ke`, a suitable default meta description, and accurate contact details.
- Test live public routes and sitemap over HTTPS. The current automated sitemap test ran without a local MySQL service, so dynamic service URLs were not integration-tested.
- Validate production pages with Google Search Console, Google's Rich Results Test and social-sharing preview debuggers.
- Review the starter Terms and Privacy pages against actual data collection, third-party services and applicable Kenyan legal requirements before relying on them as final legal documents.
