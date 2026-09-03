# AlbaTech CSS — v4 structure

## What changed vs v3

`v3/production.css` (3,300 lines) was the actual live stylesheet, but it was
built by concatenating `v2/app.css`'s import list — and that list imported
several "consolidated" component files (e.g. `admin.css`) that already
contained the full content of other files on the same list (e.g.
`dashboard.css`, `service-admin-toggles.css`). Nobody removed the originals
once they got folded into the consolidated versions, so the same rules were
being shipped to the browser twice. Confirmed byte-for-byte duplicates,
each counted once in v2/app.css as its own import AND again nested inside
a later "consolidated" file:

`base.css`, `dashboard.css`, `hero-sections.css`, `whatsapp-float.css`,
`footer.css`, `error-pages.css`, `skeletons.css`, `tables.css`, `toasts.css`,
`alerts.css`, `cards.css`, `services.css`, `service-admin-toggles.css`,
`modals.css`

v4 drops the duplicate copy of each and keeps only the consolidated file
that already contained it. That's ~800 lines gone for zero visual change —
verified by diffing every CSS selector between v3 and v4: all 1,083 unique
selectors in v3 are still present in v4, nothing was dropped, only exact
repeats were removed.

The second problem was three separate copies of the same brand color
tokens: the base `theme.css` (`--color-primary` etc., blue, mostly
superseded), then a `--v3-*` set and a `--ux-*` set — both defining the
*identical* teal palette under different variable names, bolted on in two
different rounds of work ("V3.8 Digital Assistant" and "V4.1 UX
consolidation"). v4 merges these into one `--brand-*` set declared once in
`theme.css`. Same values, one place to change them.

Also fixed: the `<meta name="theme-color">` fallback in
`layouts/app.php` still defaulted to the old blue (`#0F4C81`) while the
`--color-primary` fallback two lines below it already defaulted to teal
(`#0f766e`) — a leftover inconsistency from the rebrand. Now both default
to teal.

## Structure

```
v4/
  theme.css                    — all design tokens (colors, spacing, radii, brand)
  production.css                — the single file layout.php actually loads,
                                   built by concatenating theme.css + components/
                                   in order. Edit a component file, not this one —
                                   see "Rebuilding" below.
  components/
    base.css                    — reset, typography
    buttons.css
    forms.css
    content.css                 — cards, tables
    feedback.css                — alerts, toasts, modals, skeletons, error pages
    navigation.css               — header, sidebar, footer
    admin.css                    — dashboard, service visibility toggles
    hero.css
    catalogue.css                — service catalogue, SEO landing pages
    homepage.css
    whatsapp.css
    about-contact.css
    accessibility.css
    ui-components.css
    polish.css
    public-brand-skin.css        — the teal skin layered over the pages above
    assistance.css                — "Get Assistance" intake form + status
    quotes-payments.css           — quote editor, public quote/pay pages
    work-management.css           — admin task workflow, customer job portal
    customer-portal.css           — logged-in customer dashboard
    assistant-chat.css            — Digital Assistant chat widget
    public-ux-consolidation.css   — latest round of public-page polish
```

Component files are named by what they style, not by version/phase number
(no more `phase1-public.css`, `phase4-quality.css`, `growth-phase2.css`,
`V3.2`, `V4.1`). If you need to change how the homepage hero looks, open
`homepage.css` and `public-brand-skin.css` — not a grep across nine files
guessing which "phase" touched it last.

## Known issue not fixed here (left for a deliberate decision)

`public-brand-skin.css` and `public-ux-consolidation.css` both override
`.btn-primary` / `.btn-secondary` with `!important`, and those selectors
are **not scoped to public pages** — the original code comment claimed
"Admin remains on v2" but `.btn-primary` is used in 30 admin view files too,
so admin buttons also pick up the teal pill styling. Whether that's wanted
(one consistent button style everywhere) or not (admin should look
distinct from the public marketing brand) is a product decision, not a
CSS bug — I left it exactly as it behaves today rather than silently
changing how your admin panel looks. Tell me which way you want it and
I'll scope it properly (e.g. `.public-page .btn-primary`).

One more inconsistency, not touched: `ReceiptService.php` still hardcodes
the old blue (`#0F4C81`) for PDF payment receipts, so receipts render in
the old brand color while the site is teal. Didn't change a customer-facing
document without you confirming you want that recolored.

## Rebuilding production.css after an edit

There's no build tool wired up (no npm/composer script for this). After
editing a file in `components/`, regenerate `production.css` by
concatenating `theme.css` + the files in `components/` in the order listed
in this doc, in that order, then re-upload `production.css`. If you want,
ask me to wire up a one-command build script (Node or PHP, whichever you
don't need to install extra tooling for) so this happens automatically.

## Rollback

`v3/production.css` is untouched and still present at
`assets/css/v3/production.css` in case anything looks wrong after
deploying v4 — point `layouts/app.php` back at it if needed. Safe to
delete the whole `v3/` folder once you've confirmed v4 looks right live.

## Follow-up: the "two brand colors" you're now seeing live

The About page ("easier." in blue, navy decorative panel) and the Services
category filter (blue "All services" pill vs. teal everything else) aren't
a CSS bug in the file — they're two different color systems that both
exist and are both currently live:

1. `var(--color-primary)` — set from **Admin → Settings → Theme**, stored
   in your database. This is what `.phase6-about-hero h1 em` (the "easier."
   word) and `.catalogue-filter button[aria-pressed="true"]` (the active
   category pill) use. Your database currently has this set to the old
   blue, not the teal you rebranded to — that's why those two elements
   still render blue while nothing else does.
2. `--brand-primary` (in `theme.css`, teal, hardcoded) — used by the nav
   CTA, hero buttons, assistant chat, quote cards. Not connected to the
   admin setting at all, so changing the theme color in Settings will
   never touch these.

**Fastest fix**: go to Admin → Settings → Theme and set Primary color to
`#0f766e`. That alone fixes the About page and the services filter pill,
since both already correctly reference `var(--color-primary)`.

**The real fix** (so this can't drift apart again): point `--brand-primary`
at `var(--color-primary)` in `theme.css` instead of hardcoding it, so the
whole site — old and new components alike — follows one setting. I didn't
make that change myself because your database's current color is blue;
doing it right now would turn your nav/hero teal buttons blue until you
also update the setting. Do the Settings change first, confirm it looks
right, then ask me to wire `--brand-primary` to `--color-primary` so it
can't split again.

## Fixed: "Get Assistance" CTA text wasn't white

Root cause found via DevTools (not the black override from before — that
one really was never in this codebase). `public-brand-skin.css` had:

```css
.site-nav a{color:var(--brand-ink)!important;}
```

`.site-nav-cta` is itself an `<a>` inside `.site-nav`, so this matched it
too. Specificity of `.site-nav a` (element+class) beats `.site-nav-cta`
(single class) — and when two `!important` rules conflict, specificity
decides the winner, not source order. So the generic "make nav links
dark ink" rule always beat the CTA's "make this one white" rule,
regardless of which file loaded last.

Fixed by excluding the CTA, matching the pattern your own
`public-ux-consolidation.css` already used for the hover state:

```css
.site-nav a:not(.site-nav-cta){color:var(--brand-ink)!important;}
```
