-- 026: Two-door positioning. One brand with two clear paths from the homepage:
-- "Build & grow" (systems, websites, Google profile, SME Launchpad) and
-- "Get it sorted" (KRA, eTIMS, BRS, eCitizen, SHA, NTSA). The header gains a
-- Compliance help link to the new hub page; Services moves to the footer.

UPDATE site_settings SET setting_value = 'Software studio and compliance desk in Nairobi' WHERE setting_key = 'home_hero_eyebrow';
UPDATE site_settings SET setting_value = 'Software, websites and compliance help for Kenyan businesses.' WHERE setting_key = 'home_hero_title';
UPDATE site_settings SET setting_value = 'We build systems and websites for growing businesses, and handle the KRA, eTIMS, eCitizen and business registration tasks that slow you down. You get a clear scope and price before we start.' WHERE setting_key = 'home_hero_lead';
UPDATE site_settings SET setting_value = 'Chat on WhatsApp' WHERE setting_key = 'home_hero_primary_label';
UPDATE site_settings SET setting_value = '/get-help' WHERE setting_key = 'home_hero_primary_url';
UPDATE site_settings SET setting_value = 'Find the right help' WHERE setting_key = 'home_hero_secondary_label';
UPDATE site_settings SET setting_value = '#choose-path' WHERE setting_key = 'home_hero_secondary_url';
UPDATE site_ctas SET label = 'Chat on WhatsApp', url = '/get-help' WHERE cta_key = 'primary_enquiry';
UPDATE site_ctas SET label = 'Find the right help', url = '#choose-path' WHERE cta_key = 'explore_services';

UPDATE site_settings SET setting_value = 'Two ways we help' WHERE setting_key = 'home_services_kicker';
UPDATE site_settings SET setting_value = 'What do you need today?' WHERE setting_key = 'home_services_title';

UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Software & KRA Help Nairobi' WHERE setting_key = 'home_meta_title';
UPDATE site_settings SET setting_value = 'Software, websites and fast KRA, eTIMS, eCitizen and business registration help for Kenyan businesses and individuals. Based in Nairobi.' WHERE setting_key = 'home_meta_description';
UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Software & Compliance Help Kenya' WHERE setting_key = 'default_meta_title';
UPDATE site_settings SET setting_value = 'Nairobi software studio and compliance desk: business systems, websites and help with KRA, eTIMS, eCitizen and business registration.' WHERE setting_key = 'default_meta_description';
UPDATE site_settings SET setting_value = 'A Nairobi software studio and compliance desk. We build systems and websites for Kenyan businesses and help with KRA, eTIMS, eCitizen and business registration.' WHERE setting_key = 'footer_description';

-- Header: Solutions, Work, Products, Compliance help, Guides, Contact.
UPDATE site_navigation SET sort_order = 10 WHERE placement = 'header' AND url = '/solutions';
UPDATE site_navigation SET sort_order = 20 WHERE placement = 'header' AND url = '/work';
UPDATE site_navigation SET sort_order = 30 WHERE placement = 'header' AND url = '/products';
UPDATE site_navigation SET is_enabled = 0 WHERE placement = 'header' AND url = '/services';
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Compliance help', '/services/kra-ecitizen-help', 'header', 40, 1
WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'header' AND url = '/services/kra-ecitizen-help');

INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'SME Launchpad', '/services/sme-launchpad', 'footer_explore', 45, 1
WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/services/sme-launchpad');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Compliance help', '/services/kra-ecitizen-help', 'footer_explore', 46, 1
WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/services/kra-ecitizen-help');
