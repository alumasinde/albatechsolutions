# AlbaTech SVG Icon System

The public header already uses the local SVG sprite at `/assets/images/icons.svg`. Use it as the default for new UI icons:

```html
<svg class="icon" aria-hidden="true" focusable="false"><use href="/assets/images/icons.svg#icon-arrow-right"></use></svg>
```

Available symbols currently include `icon-arrow-right`, `icon-external-link`, `icon-bars`, `icon-xmark`, `icon-circle-info`, `icon-location`, `icon-envelope`, `icon-phone`, `icon-laptop-code`, `icon-comments`, `icon-globe`, `icon-pause`, `icon-receipt`, `icon-handshake`, and `icon-whatsapp`. Verify IDs in `public/assets/images/icons.svg` before referencing them.

## Migration policy
- Prefer the local SVG sprite for new interface icons and any existing icon with a matching symbol.
- Use official brand SVGs (or Font Awesome Brands) for third-party brand marks such as WhatsApp.
- Do not remove Font Awesome's full stylesheet yet: legacy public and admin templates still contain many solid and regular Font Awesome classes without matching SVG symbols. Removing it now would make those icons disappear.
- Migrate remaining icons in batches, adding and verifying sprite symbols before removing the legacy stylesheet.
- Do not preload the SVG sprite unless measurements show it is needed immediately. SVG `<use>` references should load the sprite normally.

## SVG sizing
The shared `.icon` class should control dimensions, alignment, and flex shrinking. Keep decorative icons `aria-hidden="true"`; give standalone icon-only buttons an accessible label.
