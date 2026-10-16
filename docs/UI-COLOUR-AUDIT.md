# UI Colour Leak Audit — 2026-10-10

## What was fixed
- Moved remaining public stylesheet literal colours into `:root` design tokens.
- Centralized the admin palette in `public/assets/css/tokens.css`; `admin.css` now references those tokens.
- Added definitions for previously missing tokens used by public/admin styles, including background aliases, WhatsApp states, warning surfaces and admin states.
- Replaced undefined CSS variables that could cause declarations to become invalid and leave backgrounds/borders in unexpected states.
- Centralized translucent overlays and shadow colours as reusable tokens.
- Preserved the existing primary brand values (`--color-primary: #078C99`, `--color-accent: #FFB72B`) and the overall page layout.
- No PHP view inline color/background styles were found in the scanned view templates.

## Files changed
- `public/assets/css/tokens.css`
- `public/assets/css/app.css`
- `public/assets/css/admin.css`

## Validation
- Checked every `var(--...)` reference across the three stylesheets; all referenced variables have definitions.
- Checked public/admin stylesheets for hardcoded hex/RGB/HSL color literals; none remain outside `tokens.css`.
- PHP syntax and application tests should still be run locally; CSS rendering requires browser validation.

## Manual browser checks
1. Public homepage, service catalogue and service detail pages.
2. Contact page filters, WhatsApp popup, and footer.
3. Guides, legal pages, and print styles.
4. Admin login, dashboard, forms, badges, alerts and navigation.
5. Mobile width and keyboard focus states.
