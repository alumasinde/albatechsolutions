INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('business_city', 'Nairobi, Kenya', 'string', 'contact', 1),
('service_area', 'Nationwide — fully online', 'string', 'contact', 1),
('contact_hours', 'Monday–Friday, 8:00 am–5:00 pm', 'string', 'contact', 1),
('contact_response_time', 'Within one business day', 'string', 'contact', 1),
('support_email', '', 'email', 'contact', 1),
('whatsapp_prefill_message', 'Hi AlbaTech Solutions, I would like to make an enquiry. Please guide me on the next steps.', 'string', 'contact', 1),
('terms_content', '', 'text', 'legal', 1),
('privacy_content', '', 'text', 'legal', 1)
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
