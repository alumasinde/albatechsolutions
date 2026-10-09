CREATE TABLE IF NOT EXISTS services (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    short_description VARCHAR(500) NOT NULL,
    description TEXT NULL,
    who_for TEXT NULL,
    what_included TEXT NULL,
    what_needed TEXT NULL,
    pricing_type VARCHAR(30) NOT NULL DEFAULT 'quote',
    pricing_amount DECIMAL(12,2) NULL,
    pricing_label VARCHAR(180) NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_services_slug (slug),
    KEY idx_services_category_status_order (category_id, status, sort_order),
    KEY idx_services_featured_status (is_featured, status),
    CONSTRAINT fk_services_category FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Business Website Design', 'website-design',
       'A clean, mobile-friendly website that helps customers understand your business and contact you.',
       'We help plan and build practical business websites with clear information, responsive layouts and simple ways for customers to reach you. Final scope is agreed before work begins.',
       'Small businesses, professionals, startups and organisations that need a clear online presence.',
       'Website structure planning, responsive page design, contact calls to action and basic on-page SEO, according to the agreed scope.',
       'Your business name, services, contact details, logo if available, and the content or images you want to use.',
       'quote', 'Request a quote', 'Business Website Design in Kenya | AlbaTech Solutions',
       'Mobile-friendly business website design in Kenya. Discuss your requirements with AlbaTech Solutions.',
       'published', 1, 10
FROM service_categories WHERE slug = 'web-design-digital-presence';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Landing Page Design', 'landing-page-design',
       'A focused page for a campaign, product, service or lead-generation offer.',
       'We create focused landing pages that explain an offer clearly and make the next action easy for visitors.',
       'Businesses, freelancers and organisations promoting a specific product, service or campaign.',
       'Page structure, responsive layout, clear calls to action and basic metadata based on the agreed scope.',
       'Your offer, target audience, contact method, copy and any brand assets available.',
       'quote', 'Request a quote', 'Landing Page Design in Kenya | AlbaTech Solutions',
       'Get a focused, mobile-friendly landing page for your business or campaign.',
       'published', 1, 20
FROM service_categories WHERE slug = 'web-design-digital-presence';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Google Business Profile Assistance', 'google-business-profile-setup',
       'Get help setting up or improving your business profile on Google, subject to Google eligibility and verification.',
       'We guide you through setting up or improving a Google Business Profile using the information and access you provide. Google controls eligibility, verification and profile decisions.',
       'Eligible local businesses that want to improve how customers find their business online.',
       'Profile information review, category and service guidance, business details and profile optimisation recommendations.',
       'Business name, address or service area, phone number, opening hours and other details required by Google.',
       'quote', 'Ask about pricing', 'Google Business Profile Setup | AlbaTech Solutions',
       'Assistance setting up and improving a Google Business Profile for eligible businesses in Kenya.',
       'published', 1, 30
FROM service_categories WHERE slug = 'web-design-digital-presence';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'KRA Returns Filing Assistance', 'kra-returns-filing',
       'Get practical assistance understanding the information and steps needed to file your KRA returns.',
       'We provide independent assistance with the KRA returns filing process based on the information you supply. Requirements vary by taxpayer and circumstances; we confirm the scope before starting.',
       'Individuals or eligible businesses seeking help understanding the return-filing process.',
       'Requirements checklist, guidance through the relevant official process and assistance within the agreed scope.',
       'Relevant taxpayer information and supporting records requested for your circumstances. Never send your iTax password or PIN through chat.',
       'quote', 'Ask about pricing', 'KRA Returns Filing Assistance in Kenya | AlbaTech Solutions',
       'Independent assistance with KRA returns filing in Kenya. Requirements depend on your tax situation.',
       'published', 1, 10
FROM service_categories WHERE slug = 'government-business-assistance';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Business Registration Assistance', 'business-registration',
       'Understand the steps and information commonly needed when registering a business in Kenya.',
       'We help you understand the business registration process and prepare the information needed for the applicable official process. Registration and approval decisions are made by the relevant authority.',
       'Entrepreneurs and small businesses planning to formalise their operations.',
       'Process guidance, a preparation checklist and assistance with the agreed application steps.',
       'Proposed business name, owner or director details and other information required by the official process.',
       'quote', 'Ask about pricing', 'Business Registration Assistance in Kenya | AlbaTech Solutions',
       'Independent guidance and assistance with business registration processes in Kenya.',
       'published', 1, 20
FROM service_categories WHERE slug = 'government-business-assistance';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'CV Writing & Improvement', 'cv-writing',
       'Improve the structure, clarity and presentation of your CV for job applications.',
       'We help organise your experience and skills into a clear CV. You remain responsible for confirming that all personal details and claims are accurate.',
       'Job seekers who want a clearer, better-structured CV.',
       'Review of supplied information, wording improvements and an agreed final document format.',
       'Your current CV if available, work history, skills, education and the roles you are targeting.',
       'quote', 'Ask about pricing', 'CV Writing and Improvement | AlbaTech Solutions',
       'CV writing and improvement assistance for job seekers in Kenya.',
       'published', 0, 10
FROM service_categories WHERE slug = 'career-documents';
