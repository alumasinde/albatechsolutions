# Phase 3.1 Public URL Audit

## Canonical public site map

- /
- /services
- /services/{service-slug}
- /about
- /contact
- /get-help
- /blog
- /blog/{slug}
- /faqs
- /robots.txt
- /sitemap.xml

## Functional, non-indexable customer URLs

- /get-help/thanks
- /quote/{token}
- /request/{token}
- /request/{token}/notifications
- /review/{token}
- /receipt/{token}

These are workflow pages, not SEO landing pages.

## Redirects

- /services/website-design-development -> /services/website-design-kenya (301)
- /services/cr12-applications -> /services/cr12-application (301)
- /services/networking -> /services/wifi-networking (301)

Unknown service URLs remain 404. Do not redirect them to the homepage.

## Retired public concepts

Projects/portfolio, digital assistant, growth intelligence and customer account landing flows are not part of the public site map.

## Phase 3.1 rule

No new public URLs should be added before a clear purpose, canonical slug, internal-link role and indexability decision are defined.
