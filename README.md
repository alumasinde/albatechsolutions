# AlbaTech Solutions

Kenya-focused digital assistance and technology services.

AlbaTech helps individuals and businesses get practical tasks done, including KRA returns support, eCitizen help, business registration assistance, CV writing, websites and practical IT services.

**Tell us the task. We'll help with the next step.**

AlbaTech Solutions is an independent business and is **not a government agency**.

## Phase 1 architecture

The active application is focused on:

- Public website and SEO service pages
- Service catalogue
- Guides/blog content
- FAQs and contact messages
- WhatsApp-first and form-based assistance requests
- Admin management
- Assistance requests, quotes, payment verification and work tracking

Retired from the active route surface:

- Customer self-registration/account portal
- Legacy Orders checkout workflow
- Legacy order payment workflow
- Public digital assistant
- Growth analytics collection
- Public projects/portfolio
- Generic public Pages CMS routing

Historical migrations are retained for deployment safety. See `docs/PHASE_1_AUDIT.md`.

## Stack

- PHP 8.2+
- Custom PHP framework
- MySQL/MariaDB
- Composer
- Public document root: `public_html/`

## Local setup

```bash
composer install
php bin/qa.php
```

Configure required values in a local `.env`. Never commit credentials, API keys or deployment secrets.

## Next

Phase 2: database baseline and real AlbaTech seed data.


## Fresh database setup

For a new AlbaTech database, use the current migrations and seed instead of copying old data.

Configure DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS and DB_CHARSET in your local .env, then run:

    composer install
    php bin/install-fresh.php

The installer creates the configured empty database, runs every migration in order, then loads the current AlbaTech seed.

To deliberately discard the configured database and rebuild it:

    php bin/install-fresh.php --reset

Warning: --reset drops the configured database.

The baseline includes dynamic content for KRA Returns Filing, eCitizen Services, Business Registration, CV Writing, Website Design, Computer Repair, IT Support and Google Business Profile Setup. All use the shared dynamic service renderer and database content; no separate hardcoded public PHP pages are needed.
