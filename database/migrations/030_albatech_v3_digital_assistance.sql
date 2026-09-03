-- Migration 030: AlbaTech v3 digital-assistance positioning.
-- Safe to re-run: settings and menu labels are updated in place.

INSERT INTO settings (`key`, `value`, `type`) VALUES
('seo_default_title', 'Digital Assistance in Kenya | AlbaTech Solutions', 'string'),
('seo_default_description', 'AlbaTech Solutions helps Kenyans get things done online — government and business services, documents, CVs, websites and practical digital solutions.', 'string'),
('seo_default_keywords', 'digital assistance Kenya, online services Kenya, KRA assistance, eCitizen assistance, business registration Kenya, SHA registration assistance, CV writing Kenya, website design Kenya, software development Kenya', 'string'),
('seo_organization_description', 'AlbaTech Solutions is a Kenya-based digital assistance and technology business helping people complete practical online tasks and helping businesses build useful digital tools.', 'string'),
('site_tagline', 'Helping Kenyans get things done digitally.', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

UPDATE menu_items mi
JOIN menus m ON m.id = mi.menu_id
SET mi.label = 'Get Help', mi.url = '/services', mi.sort_order = 1
WHERE m.slug = 'header' AND mi.url = '/services';

UPDATE menu_items mi
JOIN menus m ON m.id = mi.menu_id
SET mi.label = 'Guides', mi.url = '/blog', mi.sort_order = 3
WHERE m.slug = 'header' AND mi.url = '/blog';

UPDATE menu_items mi
JOIN menus m ON m.id = mi.menu_id
SET mi.label = 'Business Solutions', mi.url = '/projects', mi.sort_order = 4
WHERE m.slug = 'header' AND mi.url = '/projects';

INSERT INTO service_categories (name, slug, sort_order) VALUES
('Digital & Government Assistance', 'digital-assistance', 1),
('Business & Online Presence', 'business-digital', 2),
('Web, Software & Automation', 'web-software', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Move selected existing services into the new categories.
SET @assist = (SELECT id FROM service_categories WHERE slug = 'digital-assistance' LIMIT 1);
SET @business = (SELECT id FROM service_categories WHERE slug = 'business-digital' LIMIT 1);
SET @software = (SELECT id FROM service_categories WHERE slug = 'web-software' LIMIT 1);

UPDATE services SET category_id=@assist WHERE slug IN
('kra-pin-registration','kra-returns-filing','tax-compliance','ecitizen-services','business-registration','cr12-applications','ntsa-services','sha-registration','nssf-services','cv-writing');

UPDATE services SET category_id=@business WHERE slug IN
('google-business-profile-setup','business-email-setup','domain-registration','web-hosting');

UPDATE services SET category_id=@software WHERE slug IN
('website-design-development','software-development','digital-marketing','graphic-design');

-- These services are useful but should not lead the consumer-facing positioning.
UPDATE services SET is_featured=0 WHERE slug IN
('cctv-installation','networking','computer-repair','it-support','graphic-design','digital-marketing');

UPDATE services SET is_featured=1 WHERE slug IN
('kra-returns-filing','kra-pin-registration','ecitizen-services','business-registration','cv-writing','website-design-development');
