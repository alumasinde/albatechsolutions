# AlbaTech V4.1 — Public UX/UI Consolidation

## Decisions

- Primary public conversion: **Get Assistance** → `/get-help`.
- Quick human-contact shortcut: floating **WhatsApp**.
- The Digital Assistant remains in code and can be reached directly, but is not promoted in the primary navigation.
- Public header is intentionally deterministic: Home, Services, Guides, FAQs, About, Contact, Get Assistance.
- Legacy project/quote language is de-emphasized in public copy.
- Public voice is consistently first-person plural (we/us).
- Mobile sticky CTA uses Get Assistance + WhatsApp.
- Removed legacy `public_html/assets/v1.zip` from the release package.
- Removed `.env` from the release package; use `.env.example` and the server environment.

## Database

Apply migration `041_consolidate_public_navigation.sql` after the existing migrations.

## QA

Run:

```powershell
php bin/preflight.php
php bin/qa.php
php bin/db-smoke.php
```

Then manually smoke-test the public routes and customer assistance journey.
