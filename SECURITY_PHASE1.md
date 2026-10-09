# Phase 1 Security Baseline

## Implemented in this phase

- Admin route authorization now checks the database-backed `services.manage`, `categories.manage`, and `settings.manage` permissions.
- Active admin account status is rechecked on protected requests so account deactivation takes effect without waiting for a new login.
- Login throttling is applied by source IP and email/IP identity using files under `storage/cache/rate-limits`.
- Login rotates the session ID and CSRF token; idle and absolute session expiry are enforced.
- Session cookies use HttpOnly and SameSite=Lax, with Secure enabled for HTTPS and configurable with `SESSION_SECURE_COOKIE`.
- Request input ignores array/object values rather than allowing them to be cast to strings.
- Service and category fields have server-side length and format checks; site URL, contact email and WhatsApp number are validated before saving.
- Baseline security headers include CSP, anti-framing, MIME sniffing protection, referrer policy and Permissions Policy. HSTS is sent only on HTTPS.
- Production requests fail closed when APP_DEBUG is enabled, APP_URL is not HTTPS, or the configured database account is root.
- Admin responses are marked no-store.
- Admin creation requires a 12-character minimum password.
- Added `tests/security_baseline.php` for quick local checks.

## Required before launch

- Configure `.env` with `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, `SESSION_SECURE_COOKIE=true`, and a dedicated MySQL user.
- Configure the web server document root to `public/`; keep `.env`, `storage`, database dumps and logs outside public access.
- Run migrations and verify that every admin account has the appropriate role assignment.
- Test all roles against a real database, including 403 responses for missing permissions.
- Test login lockout and expiry behaviour in the actual hosting environment.
- If behind a reverse proxy, configure trusted client IP handling explicitly before relying on IP-based throttling.
- Current CSP permits `unsafe-inline` to preserve existing inline template code. Move inline scripts/styles to assets or use nonces before tightening this policy.
- Complete live integration testing, dependency review, backup/restore verification and an external security assessment.
