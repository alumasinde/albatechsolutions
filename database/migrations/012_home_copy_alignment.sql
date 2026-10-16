-- Align the database homepage hero title with the SEO-focused copy used as the view fallback.
-- The previous value ("Technology that helps you move forward.") carried no search keywords.
UPDATE site_settings
SET setting_value = 'Website Design & Business Registration in Nairobi'
WHERE setting_key = 'home_hero_title';
