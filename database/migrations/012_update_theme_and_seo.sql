-- Updates the design-system theme tokens and SEO defaults to the
-- professional-blue / warm-amber branding direction for the Kenyan
-- market (KRA, eCitizen, NTSA, SHA, NSSF-adjacent services).
-- Safe to re-run: values are upserted, not duplicated.

INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('theme_color_primary', '#0F4C81', 'string'),
    ('theme_color_secondary', '#1F2937', 'string'),
    ('theme_color_accent', '#F59E0B', 'string'),
    ('theme_color_background', '#F8FAFC', 'string'),
    ('theme_font_family', 'Inter, sans-serif', 'string'),
    ('theme_radius', '12px', 'string'),
    ('seo_default_title', 'AlbaTech Solutions | Web Design, KRA, eCitizen & Digital Services in Kenya', 'string'),
    ('seo_default_description', 'AlbaTech Solutions offers professional web design, KRA services, eCitizen assistance, business registration, IT support, software development and digital solutions for individuals and businesses across Kenya.', 'string'),
    ('seo_default_keywords', 'Web Design Kenya, KRA Services, eCitizen Services, Business Registration Kenya, IT Support Kenya, Website Development, Software Development, AlbaTech Solutions, Digital Services Kenya, Company Registration, KRA PIN, Tax Returns, NTSA Services, SHA Registration', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
