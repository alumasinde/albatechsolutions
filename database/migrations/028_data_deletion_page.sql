-- Data deletion instructions page required for Meta WhatsApp Cloud API app settings.
-- The cms_pages body stays a short placeholder so the designed data-deletion template
-- keeps rendering (same convention as the seeded 'privacy' and 'terms' pages).
INSERT IGNORE INTO cms_pages (slug,title,intro,body_content,meta_title,meta_description,status) VALUES
('data-deletion','Data Deletion','How to ask AlbaTech Solutions to delete your personal information.','You can ask us to delete personal information you have shared through this website or WhatsApp. Use the enquiry form or email us and we will confirm the request before acting on it.','Data Deletion Instructions | AlbaTech Solutions','How to request deletion of your personal information held by AlbaTech Solutions, including enquiry and WhatsApp conversation data.','published');

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('data_deletion_content', '', 'text', 'legal', 1)
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);

-- Footer "Information" links. Each row is inserted only if its URL is absent, so an
-- empty placement gains the full set while admin-managed menus keep their entries.
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled)
SELECT 'Privacy policy','/privacy','footer_information',10,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='footer_information' AND url='/privacy');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled)
SELECT 'Terms of service','/terms','footer_information',20,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='footer_information' AND url='/terms');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled)
SELECT 'Data deletion','/data-deletion','footer_information',30,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='footer_information' AND url='/data-deletion');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled)
SELECT 'Make an enquiry','/get-help','footer_information',40,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='footer_information' AND url='/get-help');
