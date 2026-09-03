# Security and SEO hardening plan

This branch applies the requested hardening in small, reviewable commits. Production fail-closed checks remain unchanged: `APP_DEBUG=false`, a 32+ character `APP_KEY`, and an HTTPS `APP_URL` are still required by `app/bootstrap.php`.

## Scope

- Normalize canonical URLs without query strings.
- Explicitly noindex private/token/account/authentication URLs.
- Keep public high-intent service/content URLs indexable.
- Customer registration always creates only customer role membership.
- Staff/admin login requires a verified email and an assigned non-customer role.
- Staff Google OAuth still honours 2FA.
- Store 2FA recovery codes as hashes.
- Restrict CSRF to `_csrf_token` form/body input.
- Keep trusted-proxy handling for client IP resolution.
- Harden assistance bearer-token routes and expiry behaviour.
- Add a server-side, allowlisted Git client and GitHub webhook endpoint.
- Improve repository hygiene and operator documentation.
