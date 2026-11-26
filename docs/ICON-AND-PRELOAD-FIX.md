# Icon and preload fix

## Root causes found
- Public templates use Font Awesome classes (`fa-solid`, `fa-regular`, `fa-brands`) but the public layout did not load the Font Awesome stylesheet. Admin layouts did load it. Those public icons therefore had no icon font/style definitions.
- `public_html/assets/images/icons.svg` used `style="display: none"` on the root SVG. This can suppress rendering when symbols are referenced from that external SVG sprite with `<use href="...#icon-name">`.
- The homepage hero image is configurable in the CMS, but the layout preloaded the fixed fallback `/assets/images/albatech-hero.webp`. When an admin selected a different image, the preloaded file was not used, producing the browser warning.

## Changes
- Added Font Awesome 6.5.2 stylesheet to the public layout.
- Removed `display:none` from the external SVG sprite root.
- Removed the hardcoded hero preload. The homepage `<img>` already has `fetchpriority="high"`, so the browser can prioritize the actual CMS-selected image without downloading a stale fallback.
- Updated the performance regression test to check the new behavior.

## Verify locally
1. Copy these changes into your project, preserving `.env`.
2. Hard refresh with Ctrl+Shift+R and DevTools Network > Disable cache.
3. Open `/assets/images/icons.svg` directly; it should return HTTP 200 and SVG content.
4. Confirm the Font Awesome CSS request returns HTTP 200 and icons appear on public pages.
5. Confirm the actual hero `<img src>` is the same CMS-selected URL and has `fetchpriority="high"`.
6. If a console warning still says `icons.svg` was preloaded, inspect the actual rendered HTML and any caching/minification layer: this project version does not declare a preload for `icons.svg`. Clear application/page cache and search templates for additional injected preload tags.
