# AlbaTech Notification Operations

## Admin alerts

When a customer submits an assistance request:

1. The request is saved first.
2. The customer confirmation notification is queued/tracked.
3. An internal WhatsApp alert is sent to `ADMIN_NOTIFICATION_WHATSAPP`.
4. An internal email alert is sent when `ADMIN_NOTIFICATION_EMAIL` is configured.
5. Every delivery is stored in `assistance_notifications`.
6. Every provider attempt is stored in `assistance_notification_attempts`.
7. Failed deliveries retry with exponential backoff.

## Production configuration

Set:

    ADMIN_NOTIFICATION_WHATSAPP=254792159806
    ADMIN_NOTIFICATION_EMAIL=your-admin-email@example.com

WhatsApp is the primary immediate alert. Email is a backup.

## Retry worker

Run every five minutes:

    */5 * * * * /usr/bin/php /path/to/albatechsolutions/database/retry-notifications.php

The database is the source of truth. A request remains visible in the admin dashboard even when a notification provider is unavailable.
