# AlbaTech final web assets

This package consolidates the CSS and JavaScript assets into one clean naming scheme.

## CSS entry points
- `css/app.css` — modular source entry point. Import this when the application loads component CSS.
- `css/production.css` — compiled, self-contained stylesheet with the same modules plus final hardening.

## CSS groups
- `foundation.css` — reset, variables and base typography
- `buttons.css` — buttons and action controls
- `forms.css` — inputs, selects, textareas and form layouts
- `content.css` — cards, tables and content surfaces
- `feedback.css` — alerts, toasts, modals, skeletons and error states
- `navigation.css` — header, sidebar and footer
- `admin.css` — dashboard and service visibility controls
- `hero.css` — reusable hero sections
- `catalogue.css` — services, projects and catalogue/SEO presentation
- `homepage.css` — homepage and conversion sections
- `whatsapp.css` — WhatsApp composer and floating CTA
- `about-contact.css` — about, contact and trust sections
- `accessibility.css` — responsive polish, motion and accessibility
- `ui-components.css` — reusable public UI primitives
- `polish.css` — final visual refinements

## JavaScript
- `site.js` — main site interactions
- `service-editor.js` — service editor behavior
- `service-toggles.js` — service list toggle progressive enhancement
- `rich-text-editor.js` — rich text editor behavior
- `security-2fa.js` — two-factor setup behavior

`app.js`, `service-form.js`, `rich-editor.js`, and `two-factor-setup.js` are retained as compatibility copies because existing PHP layouts may still reference those filenames.

## Service toggles
The Active and Homepage controls use existing server-side status/featured fields. No database migration is included or required.
