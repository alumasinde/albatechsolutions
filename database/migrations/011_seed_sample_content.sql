-- Sample content so the public site renders fully on first load.
-- Safe to run via the normal `php database/migrate.php` workflow —
-- all content is real enough to keep or edit from the admin panel,
-- not throwaway placeholder text.

-- Author every seeded page/post as the first super-admin found.
SET @author_id = (
    SELECT u.id FROM users u
    INNER JOIN user_roles ur ON ur.user_id = u.id
    INNER JOIN roles r ON r.id = ur.role_id
    WHERE r.slug = 'super-admin'
    ORDER BY u.id ASC LIMIT 1
);

-- ---------------------------------------------------------------------
-- Pages
-- ---------------------------------------------------------------------
INSERT INTO pages (title, slug, content, excerpt, status, meta_title, meta_description, author_id, published_at) VALUES
(
    'About Us',
    'about-us',
    '<h2>Who We Are</h2><p>AlbaTech Solutions is a Nairobi-based technology and government services company. We build digital tools and provide hands-on support that help businesses and individuals across Kenya get things done faster — from software development to navigating government digital services.</p><h2>What We Do</h2><p>We combine software engineering expertise with practical, on-the-ground knowledge of how government and business processes actually work in Kenya. That means we do not just build systems — we help our clients use them.</p><h2>Our Approach</h2><p>We believe technology should be reliable, secure, and easy to use. Every system we build is designed to run for years with minimal friction, backed by clear documentation and responsive support.</p>',
    'AlbaTech Solutions is a Nairobi-based technology and government services company.',
    'published',
    'About Us — AlbaTech Solutions',
    'Learn about AlbaTech Solutions, a Nairobi-based technology and government services company.',
    @author_id,
    NOW()
),
(
    'Services',
    'services',
    '<h2>What We Offer</h2><ul><li><strong>Custom Software Development</strong> — web applications, business systems, and SaaS platforms built for reliability and scale.</li><li><strong>Government Services Support</strong> — assistance navigating eCitizen, KRA, business registration, and other government digital services.</li><li><strong>IT Consulting</strong> — infrastructure planning, systems audits, and technology strategy for growing businesses.</li><li><strong>Ongoing Support &amp; Maintenance</strong> — we do not disappear after launch. Every system comes with a support plan.</li></ul><p>Get in touch to discuss which service fits what you are trying to build.</p>',
    'Custom software development, government services support, and IT consulting.',
    'published',
    'Services — AlbaTech Solutions',
    'Explore AlbaTech Solutions services: software development, government services support, and IT consulting.',
    @author_id,
    NOW()
),
(
    'Contact',
    'contact',
    '<h2>Get In Touch</h2><p>Have a project in mind or need help with a government service? Reach out and we will get back to you promptly.</p><p><strong>Email:</strong> info@albatechsolutions.co.ke<br><strong>Location:</strong> Nairobi, Kenya</p>',
    'Reach out to AlbaTech Solutions — Nairobi, Kenya.',
    'published',
    'Contact — AlbaTech Solutions',
    'Get in touch with AlbaTech Solutions in Nairobi, Kenya.',
    @author_id,
    NOW()
),
(
    'Privacy Policy',
    'privacy-policy',
    '<h2>Privacy Policy</h2><p>AlbaTech Solutions respects your privacy. This page outlines how we collect, use, and protect information shared with us through our website and services. Replace this placeholder with your finalized policy before going live.</p>',
    'How AlbaTech Solutions handles your data.',
    'draft',
    'Privacy Policy — AlbaTech Solutions',
    NULL,
    @author_id,
    NULL
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ---------------------------------------------------------------------
-- Homepage hero banner
-- ---------------------------------------------------------------------
INSERT INTO banners (title, subtitle, cta_label, cta_url, placement, sort_order, is_active) VALUES
(
    'Technology & Government Services, Built for Kenya',
    'AlbaTech Solutions helps businesses and individuals navigate digital services and build reliable software — from Nairobi, for Kenya.',
    'Get in Touch',
    '/contact',
    'homepage_hero',
    0,
    1
);

-- ---------------------------------------------------------------------
-- FAQs
-- ---------------------------------------------------------------------
INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES
('What services does AlbaTech Solutions offer?', 'We provide custom software development, government services support (eCitizen, KRA, business registration, and more), IT consulting, and ongoing technical support for businesses and individuals across Kenya.', 'General', 1, 1),
('Are you based in Nairobi?', 'Yes, AlbaTech Solutions is based in Nairobi, Kenya, and we work with clients across the country.', 'General', 2, 1),
('Do you offer support after a project is delivered?', 'Yes. Every project comes with a support plan — we do not disappear after launch.', 'Support', 3, 1),
('How do I get a quote for a project?', 'Reach out through our Contact page with a short description of what you need, and we will follow up to discuss scope and pricing.', 'Pricing', 4, 1);

-- ---------------------------------------------------------------------
-- Testimonials
-- ---------------------------------------------------------------------
INSERT INTO testimonials (client_name, client_title, client_company, quote, rating, sort_order, is_active) VALUES
('Wanjiru Kamau', 'Operations Manager', 'Nairobi Retail Group', 'AlbaTech built us a system that actually fits how we work. Support has been responsive every time we needed it.', 5, 1, 1),
('David Otieno', 'Founder', 'Otieno & Partners', 'They helped us navigate a confusing government registration process end to end. Saved us weeks of back and forth.', 5, 2, 1),
('Grace Njeri', 'IT Lead', 'Coastline Logistics', 'Solid technical work and clear communication throughout the project. Would work with them again.', 4, 3, 1);

-- ---------------------------------------------------------------------
-- Blog: category + posts
-- ---------------------------------------------------------------------
INSERT INTO blog_categories (name, slug, description) VALUES
('Company News', 'company-news', 'Updates from AlbaTech Solutions'),
('Guides', 'guides', 'Practical guides on technology and government services')
ON DUPLICATE KEY UPDATE name = VALUES(name);

SET @cat_guides = (SELECT id FROM blog_categories WHERE slug = 'guides' LIMIT 1);
SET @cat_news = (SELECT id FROM blog_categories WHERE slug = 'company-news' LIMIT 1);

INSERT INTO blog_posts (category_id, title, slug, content, excerpt, status, meta_title, meta_description, author_id, published_at) VALUES
(
    @cat_news,
    'Welcome to the New AlbaTech Solutions Website',
    'welcome-to-the-new-albatech-solutions-website',
    '<p>We are excited to launch our new website, built entirely on our own platform. Here you will find updates on our services, practical guides, and news from the AlbaTech team.</p><p>Have a look around, and reach out if you have a project in mind.</p>',
    'We are excited to launch our new website, built entirely on our own platform.',
    'published',
    'Welcome to the New AlbaTech Solutions Website',
    'AlbaTech Solutions launches its new website.',
    @author_id,
    NOW()
),
(
    @cat_guides,
    'A Quick Guide to Getting Started with eCitizen',
    'a-quick-guide-to-getting-started-with-ecitizen',
    '<p>Navigating eCitizen for the first time can be confusing. Here is a quick overview of what to expect when registering and using the platform for common government services.</p><p>If you would rather have someone handle it for you, our team can help — get in touch through our Contact page.</p>',
    'A quick overview of what to expect when using eCitizen for common government services.',
    'published',
    'A Quick Guide to Getting Started with eCitizen',
    'Practical tips for getting started with eCitizen in Kenya.',
    @author_id,
    NOW()
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ---------------------------------------------------------------------
-- Navigation: header + footer
-- ---------------------------------------------------------------------
SET @menu_header = (SELECT id FROM menus WHERE slug = 'header' LIMIT 1);
SET @menu_footer = (SELECT id FROM menus WHERE slug = 'footer' LIMIT 1);

INSERT INTO menu_items (menu_id, label, url, sort_order) VALUES
(@menu_header, 'Home', '/', 1),
(@menu_header, 'About', '/about-us', 2),
(@menu_header, 'Services', '/services', 3),
(@menu_header, 'Blog', '/blog', 4),
(@menu_header, 'FAQs', '/faqs', 5),
(@menu_header, 'Contact', '/contact', 6),
(@menu_footer, 'Privacy Policy', '/privacy-policy', 1),
(@menu_footer, 'Contact', '/contact', 2);

-- ---------------------------------------------------------------------
-- Site settings: reasonable defaults for a first look
-- ---------------------------------------------------------------------
INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('seo_default_title', 'AlbaTech Solutions — Technology & Government Services', 'string'),
    ('seo_default_description', 'AlbaTech Solutions is a Nairobi-based technology and government services company.', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
