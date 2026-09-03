-- Phase 3: SEO landing pages and visibility controls.
ALTER TABLE pages
    ADD COLUMN IF NOT EXISTS page_type VARCHAR(30) NOT NULL DEFAULT 'general' AFTER slug,
    ADD COLUMN IF NOT EXISTS focus_keyword VARCHAR(180) NULL AFTER excerpt,
    ADD COLUMN IF NOT EXISTS seo_intro VARCHAR(700) NULL AFTER focus_keyword,
    ADD COLUMN IF NOT EXISTS noindex TINYINT(1) NOT NULL DEFAULT 0 AFTER canonical_url;

INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('seo_organization_description', 'AlbaTech Solutions builds websites, custom software and digital solutions for businesses and organisations in Kenya.', 'string'),
    ('seo_default_og_image', '', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Seed only pages that do not already exist. These are editable from Admin > Pages.
INSERT INTO pages (title, slug, page_type, content, excerpt, focus_keyword, seo_intro, status, meta_title, meta_description, canonical_url, published_at)
VALUES (
      'Web Development Company in Kenya',
      'web-development-kenya',
      'service_intent',
      '<p>AlbaTech Solutions designs and develops professional, mobile-first websites for Kenyan businesses, organisations and growing brands. We focus on clear messaging, fast experiences, strong calls to action and foundations that can be expanded as your business grows.</p><h2>Websites built for real business goals</h2><p>Whether you need a simple company website, a service website, a corporate presence or a more advanced web application, we start with the customer journey and the business process behind the website.</p><h2>What we can build</h2><ul><li>Business and corporate websites</li><li>Service and professional websites</li><li>E-commerce websites</li><li>School and organisation websites</li><li>Customer portals and web applications</li><li>API and payment integrations</li></ul><h2>Built for Kenya</h2><p>Our approach considers mobile usage, local contact methods, WhatsApp enquiries and Kenyan payment workflows where required. We also build with search visibility, analytics and maintainability in mind.</p><h2>Ready to improve your online presence?</h2><p>Tell us what you need, your goals and your budget. We can recommend a practical scope instead of forcing you into a one-size-fits-all package.</p>',
      'Professional, mobile-first website development for Kenyan businesses and organisations.',
      'web development company in Kenya',
      'Professional, mobile-first website development for Kenyan businesses and organisations.',
      'published',
      'Web Development Company in Kenya | AlbaTech Solutions',
      'AlbaTech Solutions builds professional, mobile-first websites for businesses and organisations in Kenya, with SEO, analytics and conversion foundations.',
      NULL,
      NOW()
), (
      'Custom Software Development in Kenya','custom-software-development-kenya','service_intent',
      '<p>When spreadsheets, paper processes and disconnected tools start slowing a business down, custom software can turn those workflows into a structured system. AlbaTech Solutions develops business software around the way your organisation actually works.</p><h2>From workflow to working system</h2><p>We map the process first, then design the roles, permissions, data and screens needed to make the work easier to track and manage.</p><h2>Examples of business systems</h2><ul><li>Management information systems</li><li>Customer and member portals</li><li>School management systems</li><li>Inventory and operational systems</li><li>Approval and document workflows</li><li>Dashboards and reporting tools</li></ul><h2>Integrations matter</h2><p>Where appropriate, systems can connect to APIs, payment providers, notifications and existing business tools so staff do not have to duplicate work.</p><h2>Build only what you need</h2><p>We recommend a phased scope so you can launch useful functionality early and expand the platform based on real usage.</p>',
      'Custom business software designed around your organisation’s workflows.',
      'custom software development Kenya',
      'Custom business software designed around your organisation’s workflows.',
      'published','Custom Software Development in Kenya | AlbaTech Solutions',
      'Build custom business software in Kenya around your workflows, approvals, reporting, users and integrations with AlbaTech Solutions.',
      NULL,NOW()
), (
      'M-Pesa Integration for Websites & Software','mpesa-integration-kenya','service_intent',
      '<p>Kenyan customers expect convenient local payment options. AlbaTech Solutions can integrate M-Pesa payment workflows into websites and custom software where the project requires it.</p><h2>Payment flows we can support</h2><ul><li>STK Push payment journeys</li><li>Payment confirmation and callback handling</li><li>Order and payment status tracking</li><li>Receipts and transaction records</li><li>Payment-linked access or workflow actions</li></ul><h2>Designed as part of the system</h2><p>Payment integration should not be treated as an isolated button. We design the surrounding order, customer, transaction and notification flow so successful and failed payments are handled clearly.</p><h2>Security and reliability</h2><p>Server-side callbacks, validation, rate limiting and clear transaction states are important when connecting business software to payment services. The exact implementation depends on the provider and project requirements.</p>',
      'M-Pesa payment integration for Kenyan websites and business software.',
      'M-Pesa integration Kenya',
      'M-Pesa payment integration for Kenyan websites and business software.',
      'published','M-Pesa Integration Kenya | Websites & Custom Software',
      'Integrate M-Pesa payment workflows into websites and custom software for Kenyan businesses, including payment status and callback handling.',
      NULL,NOW()
), (
      'Digital Solutions for Schools in Kenya','solutions-for-schools','industry',
      '<p>Schools need more than a brochure website. They need reliable ways to manage information, communicate with parents, support staff and present a professional public presence.</p><h2>School technology solutions</h2><ul><li>School websites and admissions information</li><li>Student and parent portals</li><li>Staff workflows and approvals</li><li>Fees and payment integrations</li><li>Timetables, reporting and dashboards</li><li>Document and notification workflows</li></ul><h2>Start with the biggest bottleneck</h2><p>We can begin with a focused system or website and expand it over time. This reduces unnecessary complexity and lets the school validate each part of the solution.</p><h2>Designed for mobile access</h2><p>Parents and staff may access systems from phones, so responsive interfaces, clear navigation and efficient pages are part of the design.</p>',
      'Websites and custom digital systems for schools and education organisations in Kenya.',
      'school management system Kenya',
      'Websites and custom digital systems for schools and education organisations in Kenya.',
      'published','School Management & Website Solutions in Kenya | AlbaTech',
      'AlbaTech Solutions builds school websites, portals and custom management systems for education organisations in Kenya.',
      NULL,NOW()
), (
      'Digital Solutions for Small Businesses in Kenya','solutions-for-small-businesses','industry',
      '<p>Small businesses do not need technology for its own sake. They need practical tools that save time, improve customer experience and make important information easier to manage.</p><h2>Practical digital solutions</h2><ul><li>Professional business websites</li><li>Online enquiry and quotation forms</li><li>Customer and order workflows</li><li>Inventory and operational tools</li><li>Payment and M-Pesa integrations</li><li>Dashboards and reporting</li></ul><h2>Start small and grow</h2><p>We can design a focused first release around one important business process, then expand it when the value is proven.</p><h2>Make it easy for customers</h2><p>Clear service information, mobile-friendly pages, WhatsApp contact options and simple quote requests help turn website visitors into real enquiries.</p>',
      'Affordable, practical websites and business systems for Kenyan SMEs.',
      'digital solutions for small businesses Kenya',
      'Affordable, practical websites and business systems for Kenyan SMEs.',
      'published','Digital Solutions for Small Businesses in Kenya | AlbaTech',
      'Practical websites, custom software, payment integrations and business automation for small businesses and SMEs in Kenya.',
      NULL,NOW()
), (
      'Web Development in Nairobi','web-development-nairobi','location',
      '<p>AlbaTech Solutions provides website and digital development services for businesses and organisations in Nairobi and across Kenya. Whether your team operates from Nairobi or serves customers nationwide, we can build the digital platform around your goals.</p><h2>Services for Nairobi businesses</h2><p>Our work includes business websites, e-commerce experiences, custom web applications, business systems and integrations.</p><h2>Remote-friendly delivery</h2><p>Projects can be handled through online discovery, requirements sessions, design reviews and staged delivery. You do not need to be in the same office for a project to be managed professionally.</p><h2>Need a website or system?</h2><p>Share your current process, target customers and desired outcome. We will help define a practical scope and next steps.</p>',
      'Website development and custom digital solutions for Nairobi businesses and organisations.',
      'web development Nairobi',
      'Website development and custom digital solutions for Nairobi businesses and organisations.',
      'published','Web Development Company in Nairobi | AlbaTech Solutions',
      'Website development, custom software and digital solutions for businesses and organisations in Nairobi, Kenya.',
      NULL,NOW()
)
ON DUPLICATE KEY UPDATE slug = VALUES(slug);
