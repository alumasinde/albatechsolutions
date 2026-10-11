CREATE TABLE IF NOT EXISTS cms_pages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(120) NOT NULL,
    title VARCHAR(200) NOT NULL,
    intro TEXT NULL,
    body_content MEDIUMTEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    social_image VARCHAR(500) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'published',
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id), UNIQUE KEY uq_cms_pages_slug (slug), KEY idx_cms_pages_status (status),
    CONSTRAINT fk_cms_pages_updated_by FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_navigation (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    placement VARCHAR(20) NOT NULL DEFAULT 'header',
    sort_order INT NOT NULL DEFAULT 0,
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    open_new_tab TINYINT(1) NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id), KEY idx_site_navigation_order (placement, is_enabled, sort_order),
    CONSTRAINT fk_site_navigation_updated_by FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_ctas (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cta_key VARCHAR(100) NOT NULL,
    label VARCHAR(150) NOT NULL,
    url VARCHAR(500) NOT NULL,
    placement VARCHAR(50) NOT NULL DEFAULT 'global',
    style_class VARCHAR(80) NOT NULL DEFAULT 'button button--primary',
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id), UNIQUE KEY uq_site_ctas_key (cta_key), KEY idx_site_ctas_placement (placement, is_enabled),
    CONSTRAINT fk_site_ctas_updated_by FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO cms_pages (slug,title,intro,body_content,meta_title,meta_description,status) VALUES
('about','About AlbaTech Solutions','Practical digital help for people and businesses in Kenya.','AlbaTech Solutions helps individuals and businesses take practical next steps with websites, online presence and digital tools. We focus on clear communication, useful solutions and an agreed scope before work begins.','About AlbaTech Solutions | Practical Digital Help','Learn about AlbaTech Solutions and our practical approach to websites, online presence and digital assistance in Kenya.','published'),
('get-help','Get Assistance','Tell us what you need help with and explore available services.','Use the enquiry form below or reach us directly. We will guide you on the next step.','Get Assistance | AlbaTech Solutions','Tell AlbaTech Solutions what you need help with and explore available services.','published'),
('contact-details','Contact & Location','Find ways to reach AlbaTech Solutions.','Contact us to discuss your needs, clarify the scope and agree on the next steps.','Contact AlbaTech Solutions','Find contact and service-area information for AlbaTech Solutions in Kenya.','published'),
('terms','Terms of Service','Terms that apply when using this website and our services.','Please contact us to clarify the scope, deliverables, pricing and timelines before work begins. Any project-specific agreement takes precedence where applicable.','Terms of Service | AlbaTech Solutions','Read the terms that apply when using the AlbaTech Solutions website and services.','published'),
('privacy','Privacy Policy','How enquiries and personal information are handled.','We use information you submit to respond to enquiries and provide requested services. Do not send sensitive personal information unless it is necessary for your request. Contact us to ask about information you have submitted.','Privacy Policy | AlbaTech Solutions','Learn how AlbaTech Solutions handles enquiries and personal information.','published');

INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled) SELECT 'Home','/','header',10,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='header' AND url='/');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled) SELECT 'Services','/services','header',20,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='header' AND url='/services');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled) SELECT 'Guides','/guides','header',30,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='header' AND url='/guides');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled) SELECT 'About','/about','header',40,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='header' AND url='/about');
INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled) SELECT 'Contact','/get-help','header',50,1 WHERE NOT EXISTS (SELECT 1 FROM site_navigation WHERE placement='header' AND url='/get-help');
INSERT IGNORE INTO site_ctas (cta_key,label,url,placement,style_class,is_enabled) VALUES
('primary_enquiry','Tell us what you need','/get-help','global','button button--primary',1),
('explore_services','Explore services','/services','home_hero','button button--secondary',1);

INSERT INTO site_settings (setting_key,setting_value,value_type,setting_group,is_public) VALUES
('home_hero_eyebrow','Digital services for people and businesses in Kenya','string','homepage',1),
('home_hero_title','Technology that helps you move forward.','string','homepage',1),
('home_hero_lead','From building a professional website to improving your online business presence, get practical digital assistance with clear communication and no unnecessary complexity.','string','homepage',1),
('home_hero_image','/assets/images/albatech-social-card.png','url','homepage',1),
('home_services_kicker','Services','string','homepage',1),
('home_services_title','What can we help you get done?','string','homepage',1),
('home_work_kicker','Selected work','string','homepage',1),
('home_work_title','A look at what we have built.','string','homepage',1),
('home_work_description','Explore selected projects and practical solutions from AlbaTech Solutions.','string','homepage',1),
('home_process_kicker','How it works','string','homepage',1),
('home_process_title','A straightforward way to get started.','string','homepage',1),
('home_process_description','You do not need to know the technical details. Tell us the outcome you want, and we can work through the options together.','string','homepage',1),
('home_cta_kicker','Let us talk','string','homepage',1),
('home_cta_title','Not sure where to start?','string','homepage',1),
('home_cta_description','Tell us what you are trying to achieve. We will help you understand the next step.','string','homepage',1),
('home_step1_title','Tell us what you need','string','homepage',1),
('home_step1_description','Describe the task, challenge or idea in your own words.','string','homepage',1),
('home_step2_title','Agree on the plan','string','homepage',1),
('home_step2_description','We confirm the scope, price and expected next steps before work begins.','string','homepage',1),
('home_step3_title','Move forward with clarity','string','homepage',1),
('home_step3_description','Know what happens next and what information or action is needed from you.','string','homepage',1),
('home_why_kicker','Why AlbaTech','string','homepage',1),
('home_why_title','Practical help, without the unnecessary complexity.','string','homepage',1),
('home_why_description','Get clear guidance, sensible options and support focused on the outcome you need.','string','homepage',1),
('home_why1_title','Clear communication','string','homepage',1),
('home_why1_description','Understand the options and what each step involves.','string','homepage',1),
('home_why2_title','Know the scope','string','homepage',1),
('home_why2_description','Understand the scope and charges before agreeing to proceed.','string','homepage',1),
('home_why3_title','Easy to reach','string','homepage',1),
('home_why3_description','Start with a simple enquiry and get help deciding the next step.','string','homepage',1),
('home_areas_kicker','Work areas','string','homepage',1),
('home_areas_title','Types of projects we handle','string','homepage',1),
('home_areas_description','From websites to business processes, we focus on practical outcomes for businesses and individuals in Kenya.','string','homepage',1),
('home_area1_category','Website Design','string','homepage',1),
('home_area1_title','Business Websites','string','homepage',1),
('home_area1_description','Professional, mobile-friendly websites that help customers understand your business and contact you easily.','string','homepage',1),
('home_area2_category','Business Registration','string','homepage',1),
('home_area2_title','Company Setup','string','homepage',1),
('home_area2_description','Guidance through business registration, tax compliance setup, and understanding regulatory requirements.','string','homepage',1),
('home_area3_category','Online Presence','string','homepage',1),
('home_area3_title','Digital Visibility','string','homepage',1),
('home_area3_description','Google Business Profile setup, online directory listings, and improving how customers find your business.','string','homepage',1),
('home_meta_title','AlbaTech Solutions | Practical Digital Solutions in Kenya','string','homepage',1),
('home_meta_description','Website design, online business presence, business systems and practical digital assistance for people and businesses in Kenya. Explore services and get a clear next step.','string','homepage',1),
('home_social_image','/assets/images/albatech-social-card.png','url','homepage',1),
('home_hero_primary_label','Tell us what you need','string','homepage',1),
('home_hero_primary_url','/get-help','url','homepage',1),
('home_hero_secondary_label','Explore services','string','homepage',1),
('home_hero_secondary_url','/services','url','homepage',1),
('home_promise','We explain the scope and charges before starting. For government-related assistance, AlbaTech Solutions is an independent service provider, not a government agency. Official decisions remain with the relevant authority.','string','homepage',1)
ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);
