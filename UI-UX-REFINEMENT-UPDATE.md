# AlbaTech Solutions UI/UX refinement update

## Scope
- Refined public typography hierarchy, section spacing, hero balance, service cards, button states, navigation, and footer spacing.
- Improved mobile breakpoints for hero actions and service cards.
- Added more visible keyboard focus states and reduced-motion handling.
- Improved admin form control sizing, focus states, table overflow behavior, and compact-screen page-header layout.
- All new colors are sourced from existing CSS custom properties in `tokens.css`; the existing teal/gold brand palette is preserved.
- No database schema or application behavior changes are included.

## Files changed
- `public/assets/css/app.css`
- `public/assets/css/admin.css`

## Local verification
1. Run the app in XAMPP and hard-refresh (`Ctrl+F5`) to load the filemtime-versioned CSS.
2. Check homepage at desktop, tablet, and mobile widths.
3. Test header navigation, service cards, CTA buttons, WhatsApp action, guides, service detail pages, and enquiry forms.
4. Open `/admin` and verify dashboard, CMS editor, services, projects, guides, and settings on mobile and desktop.
5. Run the existing PHP regression scripts listed in README, especially CMS, security, SEO, contact, and performance tests.

This is a CSS/layout refinement, not a claim of a live-browser or Lighthouse audit. Recheck any page-specific components that have custom styles before production deployment.
