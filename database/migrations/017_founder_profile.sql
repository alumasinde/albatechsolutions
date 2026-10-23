-- 017: Founder profile for page-level E-E-A-T, guide author bylines and Person schema.
-- Blank-only upsert so edits saved in Admin > Settings survive later deployments.

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('founder_name', 'Albert Masinde', 'string', 'branding', 1),
('founder_title', 'Founder, AlbaTech Solutions', 'string', 'branding', 1),
('founder_bio', 'Albert Masinde founded AlbaTech Solutions to help individuals and small businesses in Kenya handle websites and digital processes without unnecessary complexity. He works directly with clients to clarify what is needed, agree the scope and costs, and see the task through. Most enquiries are handled personally, with updates on WhatsApp, phone or email.', 'text', 'branding', 1),
('founder_linkedin', '', 'url', 'branding', 1)
ON DUPLICATE KEY UPDATE
setting_value = IF(setting_value IS NULL OR setting_value = '', VALUES(setting_value), setting_value);

-- Give the seeded About page enough substance to explain the business and who runs it.
-- The original seeded copy is left alone if an administrator has edited the page.
UPDATE cms_pages SET body_content = 'AlbaTech Solutions helps individuals and businesses in Kenya take practical next steps with websites, online presence and digital tools. The work is led by Albert Masinde, who started AlbaTech Solutions to make digital tasks easier to understand and complete.

We focus on three areas: business websites and landing pages, online presence such as Google Business Profiles, and independent assistance with selected government and business processes. Most projects start with a short conversation about what you are trying to achieve, followed by a clear scope and price before any work begins.

For government-related processes, AlbaTech Solutions is an independent provider, not a government agency. We help with preparation and navigation, while decisions, requirements and processing times remain with the relevant authority.

If you are not sure which service you need, send a short enquiry. We will tell you what is realistic, what information is needed and what the next step involves.'
WHERE slug = 'about'
  AND body_content = 'AlbaTech Solutions helps individuals and businesses take practical next steps with websites, online presence and digital tools. We focus on clear communication, useful solutions and an agreed scope before work begins.';
