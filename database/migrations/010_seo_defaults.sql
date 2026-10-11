INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('default_meta_title', 'AlbaTech Solutions | Practical Digital Services in Kenya', 'string', 'seo', 1),
('default_social_image', '/assets/images/albatech-social-card.png', 'url', 'seo', 1)
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
