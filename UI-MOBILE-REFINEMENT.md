# Homepage mobile refinement — 2026-10-10

## Changed files
- resources/views/pages/home.php
- resources/views/layouts/main.php
- public/assets/css/app.css

## What changed (phones, under 640px; tablet/desktop layouts unchanged)
- Hero: shorter eyebrow/lead, meta as pill chips, "Explore services" as a text link under the WhatsApp CTA.
- Services: horizontal scroll-snap carousel (82% cards, next card peeks), dots + "1 / N" counter,
  compact cards (icon + category, title, 3-line description, price + arrow). Whole card is tappable.
  Repeated "Contact for price" pill is now plain muted text.
- How it works: vertical numbered stepper instead of three tall cards.
- Approach: copy + three compact one-line rows.
- "Types of projects we handle" section removed (duplicated Services); its dead CSS removed.
- Service promise: slim info strip. CTA padding reduced.
- Footer: duplicate legal link row removed, two-column links on phones, extra bottom padding.
- WhatsApp button: icon-only on phones; fades out while the CTA section or footer is on screen.
- Eyebrow/kicker colour now uses the link teal (better contrast). theme-color matches brand teal.
  viewport-fit=cover added so safe-area insets work.

## Notes
- Carousel JS uses the CSP nonce; no inline styles are used anywhere (style-src blocks them).
- No hardcoded colours added (all via tokens).
- Not browser-tested in this environment: check on a real phone (iPhone 14 Pro width 393px and a ~360px Android).
