-- Growth/SEO homepage defaults. Safe to re-run.
INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('homepage_eyebrow', 'Digital solutions for modern Kenyan businesses', 'string'),
    ('homepage_hero_title', 'Websites, Software & Digital Solutions That Help Your Business Grow', 'string'),
    ('homepage_hero_subtitle', 'We design and build professional websites, custom business systems and digital services for businesses and organisations across Kenya.', 'string'),
    ('homepage_primary_cta_label', 'Get a Free Quote', 'string'),
    ('homepage_primary_cta_url', '/contact', 'string'),
    ('homepage_secondary_cta_label', 'Explore Our Services', 'string'),
    ('homepage_secondary_cta_url', '/services', 'string'),
    ('homepage_services_heading', 'Solutions built around your business', 'string'),
    ('homepage_industries_heading', 'Digital solutions for different industries', 'string'),
    ('homepage_process_heading', 'From idea to launch', 'string'),
    ('analytics_ga4_id', '', 'string'),
    ('analytics_google_site_verification', '', 'string'),
    ('seo_default_title', 'Web Development, Software & Digital Services in Kenya | AlbaTech Solutions', 'string'),
    ('seo_default_description', 'AlbaTech Solutions builds professional websites, custom software and digital solutions for businesses and organisations across Kenya, with support for local digital services and integrations.', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
