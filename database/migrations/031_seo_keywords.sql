-- Search-driven keyword refresh: homepage hero, meta and footer copy aligned to
-- the terms people actually search (website design, KRA services, eTIMS,
-- eCitizen, SHA, NTSA, business registration). Targeted UPDATEs only; idempotent.

UPDATE site_settings SET setting_value = 'Websites, systems & KRA services in Nairobi' WHERE setting_key = 'home_hero_eyebrow';
UPDATE site_settings SET setting_value = 'Website design, business systems and KRA services for Kenyan businesses.' WHERE setting_key = 'home_hero_title';
UPDATE site_settings SET setting_value = 'AlbaTech designs and builds websites, booking and business systems for Kenyan SMEs, and helps with KRA returns, eTIMS, eCitizen, SHA, NTSA and business registration. One team, one message away.' WHERE setting_key = 'home_hero_lead';

UPDATE site_settings SET setting_value = 'Booking and reservation, property and rental, POS and inventory, visitor passes and custom internal tools.' WHERE setting_key = 'home_areas_description';

UPDATE site_settings SET setting_value = 'Website Design & KRA Services in Kenya | AlbaTech Solutions' WHERE setting_key = 'home_meta_title';
UPDATE site_settings SET setting_value = 'Website design, booking systems and software for Kenyan SMEs, plus help with KRA returns, eTIMS, eCitizen, SHA and business registration. Chat on WhatsApp.' WHERE setting_key = 'home_meta_description';
UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Website Design & KRA Services Kenya' WHERE setting_key = 'default_meta_title';
UPDATE site_settings SET setting_value = 'Nairobi team building websites, booking and business systems for Kenyan SMEs, with practical help on KRA returns, eTIMS, eCitizen, SHA, NTSA and registration tasks.' WHERE setting_key = 'default_meta_description';
UPDATE site_settings SET setting_value = 'Websites, business systems and practical help with KRA, eTIMS, eCitizen and other government services. Built in Nairobi, working across Kenya.' WHERE setting_key = 'footer_description';

UPDATE cms_pages SET meta_title = 'About AlbaTech Solutions | Nairobi, Kenya', meta_description = 'Who AlbaTech Solutions is: a Nairobi team building websites and business systems and helping with KRA, eTIMS, eCitizen and registration tasks.' WHERE slug = 'about';
