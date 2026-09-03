-- Deactivate the old static "Services" CMS page (slug: services) now
-- that the real service catalogue owns that URL — avoids two things
-- fighting over /services and leaves the old content in the DB
-- (soft-deleted) in case you want to recover any of its copy.
UPDATE pages SET deleted_at = NOW() WHERE slug = 'services' AND deleted_at IS NULL;

-- ---------------------------------------------------------------------
-- Categories
-- ---------------------------------------------------------------------
INSERT INTO service_categories (name, slug, sort_order) VALUES
('Web & Hosting', 'web-hosting', 1),
('Tax & KRA', 'tax-kra', 2),
('Government & Business Registration', 'government-registration', 3),
('IT Support', 'it-support', 4),
('Software, Design & Marketing', 'software-design-marketing', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

SET @cat_web = (SELECT id FROM service_categories WHERE slug = 'web-hosting' LIMIT 1);
SET @cat_tax = (SELECT id FROM service_categories WHERE slug = 'tax-kra' LIMIT 1);
SET @cat_gov = (SELECT id FROM service_categories WHERE slug = 'government-registration' LIMIT 1);
SET @cat_it = (SELECT id FROM service_categories WHERE slug = 'it-support' LIMIT 1);
SET @cat_sw = (SELECT id FROM service_categories WHERE slug = 'software-design-marketing' LIMIT 1);

-- ---------------------------------------------------------------------
-- Services — all seeded as price_type = 'quote' since no pricing was
-- supplied; edit each service in the admin panel to set a fixed price
-- or a "starting from" amount once you've settled on your rate card.
-- ---------------------------------------------------------------------
INSERT INTO services (category_id, name, slug, summary, description, icon, price_type, status, sort_order) VALUES
(@cat_web, 'Website Design & Development', 'website-design-development', 'Custom, mobile-friendly websites built for your business.', '<p>We design and build websites that look professional and work reliably on any device — from a simple business site to a custom web application.</p>', 'fa-laptop-code', 'quote', 'published', 1),
(@cat_web, 'Domain Registration', 'domain-registration', 'Get your .co.ke or .com domain name registered and configured.', '<p>We help you choose and register the right domain name for your business, and configure it correctly so your website and email work from day one.</p>', 'fa-globe', 'quote', 'published', 2),
(@cat_web, 'Web Hosting', 'web-hosting', 'Reliable hosting for your website, with support included.', '<p>Fast, reliable hosting with ongoing support — no need to manage servers or technical configuration yourself.</p>', 'fa-server', 'quote', 'published', 3),
(@cat_web, 'Business Email Setup', 'business-email-setup', 'Professional @yourcompany.co.ke email addresses.', '<p>We set up professional email addresses on your own domain, configured on your phone and computer, so you look credible and keep your business communication organized.</p>', 'fa-envelope', 'quote', 'published', 4),

(@cat_tax, 'KRA PIN Registration', 'kra-pin-registration', 'Get your KRA PIN registered correctly the first time.', '<p>We handle KRA PIN registration for individuals and businesses, making sure your details are correctly set up to avoid issues later.</p>', 'fa-id-card', 'quote', 'published', 5),
(@cat_tax, 'KRA Returns Filing', 'kra-returns-filing', 'Stay compliant with timely, accurate tax returns filing.', '<p>We file your KRA returns accurately and on time, whether you are an individual, a small business, or nil-filing.</p>', 'fa-file-invoice-dollar', 'quote', 'published', 6),
(@cat_tax, 'Tax Compliance', 'tax-compliance', 'Ongoing support to keep your tax affairs in order.', '<p>Beyond one-off filing, we help individuals and businesses stay compliant year-round, avoiding penalties and last-minute scrambles.</p>', 'fa-scale-balanced', 'quote', 'published', 7),

(@cat_gov, 'eCitizen Services', 'ecitizen-services', 'Assistance navigating eCitizen for common government services.', '<p>We help you navigate eCitizen for the services you need — from passport applications to certificates — so you avoid the confusion of doing it alone.</p>', 'fa-landmark', 'quote', 'published', 8),
(@cat_gov, 'Business Registration', 'business-registration', 'Register your business name or company, start to finish.', '<p>We handle the registration process for sole proprietorships, partnerships, and limited companies, so your business is properly and legally set up.</p>', 'fa-building', 'quote', 'published', 9),
(@cat_gov, 'CR12 Applications', 'cr12-applications', 'Get an official CR12 document for your registered company.', '<p>We process CR12 applications for registered companies, needed for tenders, bank accounts, and verifying company directors/shareholders.</p>', 'fa-file-contract', 'quote', 'published', 10),
(@cat_gov, 'NTSA Services', 'ntsa-services', 'Vehicle registration, transfers, and other NTSA services.', '<p>We assist with NTSA-related services including vehicle registration, ownership transfers, and driving licence matters.</p>', 'fa-car', 'quote', 'published', 11),
(@cat_gov, 'SHA Registration', 'sha-registration', 'Get registered with the Social Health Authority.', '<p>We help individuals and employers complete SHA registration correctly and efficiently.</p>', 'fa-notes-medical', 'quote', 'published', 12),
(@cat_gov, 'NSSF Services', 'nssf-services', 'Registration and support for NSSF contributions.', '<p>We assist with NSSF registration and related processes for both individuals and employers.</p>', 'fa-piggy-bank', 'quote', 'published', 13),

(@cat_it, 'IT Support', 'it-support', 'Ongoing technical support for your business systems.', '<p>Responsive IT support for your business — troubleshooting, maintenance, and advice, so technology problems do not become business problems.</p>', 'fa-headset', 'quote', 'published', 14),
(@cat_it, 'Computer Repair', 'computer-repair', 'Diagnosis and repair for desktops and laptops.', '<p>We diagnose and fix common hardware and software issues on desktops and laptops, with honest advice on repair vs. replace.</p>', 'fa-screwdriver-wrench', 'quote', 'published', 15),
(@cat_it, 'CCTV Installation', 'cctv-installation', 'Security camera installation for homes and businesses.', '<p>We supply and install CCTV systems tailored to your property, with remote viewing set up on your phone.</p>', 'fa-video', 'quote', 'published', 16),
(@cat_it, 'Networking', 'networking', 'Reliable wired and wireless networks for your office.', '<p>We design and set up office networks — wired and Wi-Fi — so your team stays connected without dropouts or dead zones.</p>', 'fa-network-wired', 'quote', 'published', 17),

(@cat_sw, 'Software Development', 'software-development', 'Custom software and business systems built to fit how you work.', '<p>We build custom software — from internal business tools to full platforms — designed around how your business actually operates.</p>', 'fa-code', 'quote', 'published', 18),
(@cat_sw, 'Graphic Design', 'graphic-design', 'Logos, branding, and marketing materials that look professional.', '<p>From logos to social media graphics and print materials, we design visuals that represent your business well.</p>', 'fa-palette', 'quote', 'published', 19),
(@cat_sw, 'CV Writing', 'cv-writing', 'Professional, well-structured CVs that get noticed.', '<p>We write and design clear, professional CVs tailored to the roles you are applying for.</p>', 'fa-file-lines', 'quote', 'published', 20),
(@cat_sw, 'Digital Marketing', 'digital-marketing', 'Grow your online presence with a focused digital strategy.', '<p>Social media management, online advertising, and content support to help your business reach more customers online.</p>', 'fa-bullhorn', 'quote', 'published', 21),
(@cat_sw, 'Google Business Profile Setup', 'google-business-profile-setup', 'Get found on Google Search and Maps.', '<p>We set up and optimize your Google Business Profile so customers can find you on Google Search and Maps, complete with hours, photos, and reviews.</p>', 'fa-map-location-dot', 'quote', 'published', 22)
ON DUPLICATE KEY UPDATE name = VALUES(name);
