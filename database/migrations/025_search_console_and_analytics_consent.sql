INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('google_site_verification', '', 'string', 'seo', 1),
('analytics_consent_enabled', '1', 'boolean', 'analytics', 1)
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
