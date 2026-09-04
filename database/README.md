# AlbaTech Database

Run migrations:

    php database/migrate.php

Seed the AlbaTech baseline:

    php database/seed.php

The seed is idempotent and uses only real AlbaTech categories, services and settings. It does not create fake testimonials, projects, statistics or administrator credentials.

Active model:

Service Category -> Service -> Assistance Request -> Quote -> Payment Verification -> Work -> Completion

See docs/PHASE_2_DATABASE_AUDIT.md. Historical migrations remain append-only for deployed installation safety.
