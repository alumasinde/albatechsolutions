INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('default_meta_title', 'AlbaTech Solutions | Practical Digital Services in Kenya', 'string', 'seo', 1),
('default_social_image', '/assets/images/albatech-social-card.png', 'url', 'seo', 1),
('home_meta_title', 'Website Design & Business Registration in Nairobi | AlbaTech Solutions', 'string', 'seo', 1),
('home_meta_description', 'Professional website design, business registration, KRA returns filing, and digital assistance services in Nairobi, Kenya. Clear pricing and practical solutions.', 'string', 'seo', 1),
('home_hero_image', '/assets/images/albatech-hero.webp', 'url', 'branding', 1)
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
