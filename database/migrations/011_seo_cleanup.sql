-- 011: SEO and settings cleanup.
-- The 010 upsert was a no-op (ON DUPLICATE KEY UPDATE setting_key=setting_key), so these values
-- are applied explicitly here. Migration files already applied remain untouched.

-- Homepage hero should use the lightweight WebP, not the larger PNG social card.
UPDATE site_settings SET setting_value = '/assets/images/albatech-hero.webp'
WHERE setting_key = 'home_hero_image';

-- Sharpen Kenya-focused homepage metadata.
UPDATE site_settings SET setting_value = 'Website Design, Business Registration & Digital Services in Nairobi | AlbaTech Solutions'
WHERE setting_key = 'home_meta_title';
UPDATE site_settings SET setting_value = 'Affordable website design, business registration, KRA returns filing and practical digital services for businesses in Nairobi and across Kenya. Clear pricing and fast WhatsApp support.'
WHERE setting_key = 'home_meta_description';

-- Remove settings no longer read anywhere: meta keywords are ignored by search engines,
-- founder_name was never rendered, and opening_hours only drove a fabricated schema block.
DELETE FROM site_settings WHERE setting_key IN ('founder_name', 'default_meta_keywords', 'opening_hours');
