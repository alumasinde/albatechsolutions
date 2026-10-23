-- 015: Public contact channels for the v2 launch.
-- Values match the details published on the current production site.
-- Existing non-empty values win, so edits saved in Admin > Settings are never overwritten.

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('whatsapp_number', '254792159806', 'string', 'contact', 1),
('contact_email', 'info@albatechsolutions.co.ke', 'email', 'contact', 1),
('support_email', 'info@albatechsolutions.co.ke', 'email', 'contact', 1),
('business_phone', '+254 792 159 806', 'string', 'contact', 1)
ON DUPLICATE KEY UPDATE
setting_value = IF(setting_value IS NULL OR setting_value = '', VALUES(setting_value), setting_value);
