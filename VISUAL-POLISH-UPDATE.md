# Visual polish update

- Reduced homepage case-study image height while retaining a wide crop and consistent image sizing.
- Adjusted desktop footer columns to give the contact/email column more width; the email link stays on one line at desktop widths and can wrap naturally on narrow screens.
- Kept the floating WhatsApp action icon-only and direct to WhatsApp. Added a subtle periodic glow, hover/focus feedback, and reduced-motion support.
- No database migrations or environment changes are required.

After extracting, hard-refresh the browser (Ctrl+F5) to bypass cached CSS. Check the footer at desktop widths and confirm the floating WhatsApp icon opens the configured WhatsApp URL.
