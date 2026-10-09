# Phase 1 Security Correctness — implementation and verification

## Implemented controls
- Router-level CSRF validation applies to every POST under `/admin`, before controller dispatch. Controllers retain their local checks as defense in depth. Missing/invalid tokens return HTTP 419 and do not invoke the handler.
- Session cookies use HttpOnly, SameSite=Lax, strict session mode, cookie-only IDs, disabled URL session IDs, configurable idle/absolute expiry, and HTTPS-aware Secure cookies. Session destruction expires the cookie with matching attributes.
- Successful authentication regenerates the session ID and rotates the CSRF token. Active account state is rechecked on protected requests.
- Database calls in the admin controller/repositories use PDO prepared statements for values; fixed SQL fragments are used for query structure. Never bind identifiers; allowlist them if dynamic identifiers are introduced.
- Rate-limit persistence errors now fail closed instead of silently disabling login throttling. Ensure `storage/cache/rate-limits` is writable by the PHP process and not web-accessible.
- CSP uses a per-response nonce for required inline scripts and no longer permits `unsafe-inline` in `script-src`; inline event handlers were removed. Style policy no longer allows inline styles. Additional browser security headers are enabled.
- Production startup checks reject debug mode, non-HTTPS APP_URL and the MySQL root account.

## Regression commands
```powershell
php tests/security_baseline.php
php tests/admin_workflows_baseline.php
php tests/security_phase1_regression.php
```

## Required manual integration checks before production
1. Sign in with valid credentials; verify session ID changes and CSRF token changes.
2. Submit every admin POST form without a token and with a modified token; expect HTTP 419 and no database changes.
3. Sign in as a user lacking `services.manage`, `categories.manage`, or `settings.manage`; verify direct GET and POST access returns 403.
4. Deactivate a signed-in account in the database; its next protected request must invalidate the session.
5. Trigger rate limits from a test IP/identity; expect HTTP 429 and a `Retry-After` header. Temporarily make limiter storage unwritable in staging and confirm authentication fails closed.
6. Verify logout invalidates the session and expires the cookie; replay the old cookie and confirm it cannot access admin routes.
7. Inspect response headers on HTTP and HTTPS. HSTS must only be present on HTTPS. Confirm all required CSS, JS and Font Awesome assets load under CSP without console violations.
8. Audit every SQL query whenever a repository/controller changes; all user values must be bound parameters.
9. Ensure `.env`, logs, rate-limit files and database backups are outside the public document root or explicitly denied by the web server.
10. Run tests against a disposable MySQL database before deploying.

## Important scope note
The included automated regression tests cover framework-level behavior without requiring production credentials. They do not replace a live MySQL/browser test or an independent penetration test.
