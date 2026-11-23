-- 023: Reposition public copy around the software studio and its products.
-- Navigation leads with Work, Products and Solutions; homepage, About and the
-- founder profile explain the product and systems work first.

UPDATE site_navigation SET is_enabled = 0 WHERE placement = 'header' AND url IN ('/', '/about');
UPDATE site_navigation SET sort_order = 40 WHERE placement = 'header' AND url = '/services';
UPDATE site_navigation SET sort_order = 50 WHERE placement = 'header' AND url = '/guides';
UPDATE site_navigation SET sort_order = 60 WHERE placement = 'header' AND url = '/get-help';

INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Work', '/work', 'header', 10, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'header' AND url = '/work');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Products', '/products', 'header', 20, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'header' AND url = '/products');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Solutions', '/solutions', 'header', 30, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'header' AND url = '/solutions');

INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Our work', '/work', 'footer_explore', 10, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/work');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Products', '/products', 'footer_explore', 20, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/products');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Solutions', '/solutions', 'footer_explore', 30, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/solutions');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Services', '/services', 'footer_explore', 40, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/services');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'Guides', '/guides', 'footer_explore', 50, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/guides');
INSERT INTO site_navigation (label, url, placement, sort_order, is_enabled)
SELECT 'About', '/about', 'footer_explore', 60, 1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement = 'footer_explore' AND url = '/about');

UPDATE site_settings SET setting_value = 'Software studio in Nairobi'
WHERE setting_key = 'home_hero_eyebrow' AND setting_value = 'Digital services for people and businesses in Kenya';
UPDATE site_settings SET setting_value = 'We build software and websites for Kenyan businesses.'
WHERE setting_key = 'home_hero_title' AND setting_value = 'Website Design & Business Registration in Nairobi';
UPDATE site_settings SET setting_value = 'We build our own products, like PesaLens and Budget254, and develop booking, rental and operations systems for Kenyan businesses. Websites and digital assistance are part of the same practical approach.'
WHERE setting_key = 'home_hero_lead' AND setting_value = 'From building a professional website to improving your online business presence, get practical digital assistance with clear communication and no unnecessary complexity.';
UPDATE site_settings SET setting_value = 'Explore our products' WHERE setting_key = 'home_hero_primary_label';
UPDATE site_settings SET setting_value = '/products' WHERE setting_key = 'home_hero_primary_url';
UPDATE site_settings SET setting_value = 'See our work' WHERE setting_key = 'home_hero_secondary_label';
UPDATE site_settings SET setting_value = '/work' WHERE setting_key = 'home_hero_secondary_url';

UPDATE site_settings SET setting_value = 'Digital assistance' WHERE setting_key = 'home_services_kicker';
UPDATE site_settings SET setting_value = 'Practical help beyond software.' WHERE setting_key = 'home_services_title';
UPDATE site_settings SET setting_value = 'Client work' WHERE setting_key = 'home_work_kicker';
UPDATE site_settings SET setting_value = 'Systems built for real businesses.' WHERE setting_key = 'home_work_title';
UPDATE site_settings SET setting_value = 'Selected projects, the problem each one solved and how we built it.' WHERE setting_key = 'home_work_description';
UPDATE site_settings SET setting_value = 'Systems we build' WHERE setting_key = 'home_areas_kicker';
UPDATE site_settings SET setting_value = 'Software for how your business runs.' WHERE setting_key = 'home_areas_title';
UPDATE site_settings SET setting_value = 'Booking, property, visitor, stock and internal tools, built around the process you already use.' WHERE setting_key = 'home_areas_description';

UPDATE site_settings SET setting_value = 'Software Company in Nairobi | AlbaTech Solutions'
WHERE setting_key = 'home_meta_title';
UPDATE site_settings SET setting_value = 'Software studio in Nairobi building PesaLens, Budget254 and custom booking, rental and operations systems for Kenyan businesses. See our products and work.'
WHERE setting_key = 'home_meta_description';

UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Software Studio in Nairobi'
WHERE setting_key = 'default_meta_title';
UPDATE site_settings SET setting_value = 'Software studio in Nairobi building products, business systems and websites for Kenyan businesses.'
WHERE setting_key = 'default_meta_description';

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('footer_description', 'A software studio in Nairobi building products like PesaLens and Budget254, plus booking, rental and operations systems for Kenyan businesses.', 'string', 'general', 1)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

UPDATE site_settings SET setting_value = 'Albert Masinde founded AlbaTech Solutions, a software studio in Nairobi. He builds the company''s own products and works directly with client businesses on booking, rental and operations systems, websites and practical digital assistance. Most enquiries are handled personally, with updates on WhatsApp, phone or email.'
WHERE setting_key = 'founder_bio';

UPDATE cms_pages SET
    title = 'About AlbaTech Solutions',
    intro = 'A software studio in Nairobi building products and systems for Kenyan businesses.',
    body_content = 'AlbaTech Solutions is a software studio based in Nairobi. We build our own products and we build systems for client businesses. The work is led by Albert Masinde, who started AlbaTech Solutions to make useful software without unnecessary complexity.

Our own products include PesaLens, an Android app that turns M-Pesa messages into a private spending record on the phone, and Budget254, an online toolkit for Kenyan PAYE and M-Pesa cost checks. We are also building a rental and property management platform with landlord and tenant portals, currently in early access. Each product is labelled live, beta or early access so you know where it stands.

For client businesses we build booking and reservation systems, property and rental tools, visitor and gate pass systems, POS and inventory tools and custom internal systems. Recent work includes the Big Kahuna Car Hire website, with fleet browsing, an online booking flow and an admin area for the team. We also build websites and provide practical digital assistance with business registration, KRA returns and eTIMS when that is what the task needs.

Most work starts with a short conversation about the process you want to improve, followed by a clear scope, price and delivery plan before development begins. We keep the scope honest, explain what is included and hand over work your team can actually use.

For government-related processes, AlbaTech Solutions is an independent provider, not a government agency. We help with preparation and navigation, while decisions, requirements and processing times remain with the relevant authority.',
    meta_title = 'About AlbaTech Solutions | Software Studio in Nairobi',
    meta_description = 'AlbaTech Solutions is a software studio in Nairobi building PesaLens, Budget254 and business systems, alongside websites and practical assistance.'
WHERE slug = 'about';

UPDATE services SET is_featured = 0
WHERE slug IN ('landing-page-design', 'google-business-profile-setup', 'etims-invoice-support') AND is_featured = 1;
