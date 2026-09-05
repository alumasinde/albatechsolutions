# Phase 3.4 — Public UX and Visual System Audit

Status: Audit complete. No visual rebuild has been started.

## Executive decision

The public site has a usable data and route foundation, but the visual layer is carrying several generations of styling. Preserve the dynamic public architecture and reduce CSS to one clearly owned public design system.

## Keep

- Dynamic public layout shell.
- Database-driven logo, contact details, social links and footer menu.
- Dynamic homepage data: featured services, guides and FAQs.
- Shared service renderer and service slug route.
- Service category discovery and search/filter behaviour.
- Mobile navigation toggle.
- WhatsApp quick contact and mobile CTA bar.
- Canonical URLs, JSON-LD, sitemap and robots handling.
- Legacy service redirects and alias lookup.
- Independent-assistance disclaimer.
- Get Assistance as the primary conversion path.

## Merge

### Public CSS tokens
Base color tokens and brand tokens overlap. Keep one semantic token layer and map public brand tokens to it.

### Buttons
Primary and secondary buttons are defined in the base layer and overridden by public skin layers. Public and admin button styling should be scoped separately.

### Containers
Container tokens duplicate the same layout concern. Use one public container token.

### Public cards
Homepage, catalogue and service detail patterns should converge on a small set of card primitives.

### CTA behaviour
Header CTA, hero CTA, floating WhatsApp and mobile CTA should use one hierarchy: Get Assistance, then WhatsApp, then service detail actions.

## Retire

- Phase/version names as primary public CSS vocabulary where they only describe implementation history.
- Blue fallback assumptions from older theme generations.
- Unscoped public important overrides that can affect admin views.
- Historical duplicate rules appended by redesign phases.
- Quote as a public primary navigation concept where Get Assistance owns the main journey. Keep private quote token pages.

## Navigation decision

Current navigation is understandable but broad for the primary conversion path. Recommended final hierarchy:

Desktop: Services, How It Works, Guides, About, Contact, Get Assistance.

FAQs remain indexable and linked from footer/content rather than competing with Services and Get Assistance in every primary navigation state.

## Homepage decision

Keep the existing dynamic content model:

1. Hero
2. Task-based entry points
3. Service groups
4. Featured services
5. How it works
6. Independent-assistance trust note
7. Helpful guides
8. FAQs
9. Final WhatsApp/Get Assistance CTA

Visual problems to solve: multiple class systems, repeated card patterns, inconsistent CTA semantics and trust/disclaimer treatment.

## CSS dependency assessment

The deployed stylesheet is public_html/assets/css/v4/production.css. Component sources exist, but no automated rebuild command is wired. This creates source/deployed drift risk.

Public brand overrides must be scoped away from admin views.

## Recommended implementation order

1. Lock Keep/Merge/Retire decisions.
2. Add one-command CSS build process.
3. Establish one public token layer.
4. Scope public styling away from admin.
5. Rebuild header, navigation and mobile CTA.
6. Consolidate buttons, containers and cards.
7. Rebuild homepage using existing dynamic content.
8. Rebuild services index.
9. Rebuild shared service detail page.
10. Rebuild About and Contact.
11. Verify mobile behaviour and accessibility.
12. Run QA and fresh-database verification again.

## Constraint

Do not replace dynamic service, FAQ, menu, settings, guide or CTA data with hardcoded page content during the visual rebuild.
