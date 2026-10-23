-- 016: The old social card was a WebP file saved with a .png extension, which some
-- link-preview crawlers mishandled. Replace it with a real 1200x630 JPEG.
UPDATE site_settings SET setting_value = '/assets/images/albatech-social-card.jpg'
WHERE setting_key IN ('default_social_image', 'home_social_image')
  AND setting_value = '/assets/images/albatech-social-card.png';
