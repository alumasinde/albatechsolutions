# Service URL Redirect Map

Canonical public service URLs use the current service slug:

    /services/{canonical-slug}

Legacy URLs should redirect with HTTP 301 before the service route is matched.

| Legacy URL | Canonical URL |
|---|---|
| /services/website-design-development | /services/website-design-kenya |
| /services/cr12-applications | /services/cr12-application |
| /services/networking | /services/wifi-networking |

Do not redirect a URL merely because a keyword sounds better. Existing URLs remain canonical until the corresponding service page and redirect have been reviewed.

## Rules

- Use a permanent 301 redirect for a confirmed retired slug.
- Preserve query strings where the server/router supports it.
- Do not redirect unknown service slugs to the homepage.
- Unknown service slugs should remain 404.
- Sitemap and canonical URLs must use only the current slug.
