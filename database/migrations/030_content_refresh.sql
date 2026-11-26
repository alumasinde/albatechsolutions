-- Content and SEO refresh: sharper homepage positioning, meta copy and CMS page
-- snippets. Targeted UPDATEs only; unaffected keys and admin edits elsewhere are
-- left alone. Idempotent: re-running produces the same values.

UPDATE site_settings SET setting_value = 'Nairobi software studio & compliance desk' WHERE setting_key = 'home_hero_eyebrow';
UPDATE site_settings SET setting_value = 'Websites, business systems and compliance help for Kenyan businesses.' WHERE setting_key = 'home_hero_title';
UPDATE site_settings SET setting_value = 'AlbaTech designs and builds the websites and systems Kenyan businesses run on, and handles the KRA, eTIMS, eCitizen and registration tasks that slow you down. One team, one message away.' WHERE setting_key = 'home_hero_lead';
UPDATE site_settings SET setting_value = 'Chat on WhatsApp' WHERE setting_key = 'home_hero_primary_label';
UPDATE site_settings SET setting_value = 'What do you need done?' WHERE setting_key = 'home_services_title';
UPDATE site_settings SET setting_value = 'Two ways we help' WHERE setting_key = 'home_services_kicker';

UPDATE site_settings SET setting_value = 'Software that matches how you already work.' WHERE setting_key = 'home_areas_title';
UPDATE site_settings SET setting_value = 'Booking, property, visitor, stock and internal tools, shaped around the process your team already follows.' WHERE setting_key = 'home_areas_description';
UPDATE site_settings SET setting_value = 'Systems we build' WHERE setting_key = 'home_areas_kicker';

UPDATE site_settings SET setting_value = 'Real systems for real businesses.' WHERE setting_key = 'home_work_title';
UPDATE site_settings SET setting_value = 'Selected projects: the problem each one solved and how we built it.' WHERE setting_key = 'home_work_description';
UPDATE site_settings SET setting_value = 'Client work' WHERE setting_key = 'home_work_kicker';

UPDATE site_settings SET setting_value = 'Clear steps. No jargon.' WHERE setting_key = 'home_process_title';
UPDATE site_settings SET setting_value = 'You do not need to know the technical details. Tell us the outcome and we handle the rest.' WHERE setting_key = 'home_process_description';
UPDATE site_settings SET setting_value = 'Tell us what you need' WHERE setting_key = 'home_step1_title';
UPDATE site_settings SET setting_value = 'Describe the task or outcome in your own words, by WhatsApp, call or the enquiry form.' WHERE setting_key = 'home_step1_description';
UPDATE site_settings SET setting_value = 'Agree the plan and price' WHERE setting_key = 'home_step2_title';
UPDATE site_settings SET setting_value = 'We confirm the scope, price and next steps in writing before work begins.' WHERE setting_key = 'home_step2_description';
UPDATE site_settings SET setting_value = 'We build, you review' WHERE setting_key = 'home_step3_title';
UPDATE site_settings SET setting_value = 'Work is delivered in agreed stages so you always know what happens next.' WHERE setting_key = 'home_step3_description';

UPDATE site_settings SET setting_value = 'One accountable team for build and admin.' WHERE setting_key = 'home_why_title';
UPDATE site_settings SET setting_value = 'Clear guidance, sensible options and support focused on the outcome you need.' WHERE setting_key = 'home_why_description';
UPDATE site_settings SET setting_value = 'Plain-language updates' WHERE setting_key = 'home_why1_title';
UPDATE site_settings SET setting_value = 'You always know what is happening, why, and what comes next.' WHERE setting_key = 'home_why1_description';
UPDATE site_settings SET setting_value = 'Agreed scope and price' WHERE setting_key = 'home_why2_title';
UPDATE site_settings SET setting_value = 'Charges are confirmed in writing before any work starts.' WHERE setting_key = 'home_why2_description';
UPDATE site_settings SET setting_value = 'WhatsApp-fast replies' WHERE setting_key = 'home_why3_title';
UPDATE site_settings SET setting_value = 'Message us like you would a colleague and get a clear next step.' WHERE setting_key = 'home_why3_description';

UPDATE site_settings SET setting_value = 'Not sure where to start?' WHERE setting_key = 'home_cta_title';
UPDATE site_settings SET setting_value = 'Tell us what you are trying to get done. We will suggest the simplest next step.' WHERE setting_key = 'home_cta_description';

UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Websites, Systems & KRA Help Kenya' WHERE setting_key = 'home_meta_title';
UPDATE site_settings SET setting_value = 'Websites, booking systems and business software for Kenyan businesses, plus fast KRA, eTIMS, eCitizen and registration help. Based in Nairobi. Chat on WhatsApp.' WHERE setting_key = 'home_meta_description';
UPDATE site_settings SET setting_value = 'AlbaTech Solutions | Software & Compliance Help, Kenya' WHERE setting_key = 'default_meta_title';
UPDATE site_settings SET setting_value = 'Nairobi software studio building websites, booking systems and business tools, with practical KRA, eTIMS, eCitizen and business registration support.' WHERE setting_key = 'default_meta_description';
UPDATE site_settings SET setting_value = 'A Nairobi software studio and compliance desk. We build websites and business systems for Kenyan companies and handle the KRA, eTIMS, eCitizen and registration tasks in between.' WHERE setting_key = 'footer_description';

UPDATE cms_pages SET meta_title = 'About AlbaTech Solutions | Software Studio, Nairobi', meta_description = 'Who AlbaTech Solutions is: a Nairobi software studio and compliance desk helping Kenyan businesses build online and stay on top of KRA, eTIMS and eCitizen tasks.' WHERE slug = 'about';
UPDATE cms_pages SET meta_title = 'Get Help | AlbaTech Solutions Kenya', meta_description = 'Reach AlbaTech Solutions by WhatsApp, phone or enquiry form for websites, business systems and KRA, eTIMS, eCitizen or registration tasks across Kenya.' WHERE slug = 'get-help';
UPDATE cms_pages SET meta_title = 'Contact & Location | AlbaTech Solutions Kenya', meta_description = 'WhatsApp, phone and email details for AlbaTech Solutions in Nairobi, with remote support for businesses and individuals across Kenya.' WHERE slug = 'contact-details';
