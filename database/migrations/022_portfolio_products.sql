-- 022: Reposition the portfolio around software products and client systems.
-- Adds case-study/product fields, a systems service category, five solution
-- services and four portfolio entries (Big Kahuna, Budget254, PesaLens, Rental).
-- The gate pass system is published as a solution service, not a live product.

ALTER TABLE work_projects
    ADD COLUMN kind VARCHAR(20) NOT NULL DEFAULT 'project' AFTER category_id,
    ADD COLUMN lifecycle_status VARCHAR(20) NOT NULL DEFAULT 'live' AFTER status,
    ADD COLUMN platform VARCHAR(120) NULL AFTER lifecycle_status,
    ADD COLUMN download_url VARCHAR(500) NULL AFTER platform,
    ADD COLUMN challenge TEXT NULL AFTER description,
    ADD COLUMN solution TEXT NULL AFTER challenge,
    ADD COLUMN features_json TEXT NULL AFTER solution,
    ADD COLUMN result TEXT NULL AFTER features_json,
    ADD COLUMN stack_json TEXT NULL AFTER result,
    ADD COLUMN gallery_json TEXT NULL AFTER stack_json,
    ADD COLUMN privacy_note TEXT NULL AFTER gallery_json,
    ADD KEY idx_work_projects_kind (kind, status, is_featured, sort_order);

INSERT IGNORE INTO service_categories (name, slug, description, status, sort_order) VALUES
('Software & Business Systems', 'software-business-systems', 'Custom systems that replace manual booking, property, visitor, stock and approval processes.', 'active', 5);

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Booking & Reservation Systems', 'booking-reservation-systems',
       'Online booking and reservation systems for car hire, accommodation, tours and service businesses.',
       'We build booking systems around the way your business already takes reservations. That usually starts with a review of how bookings arrive today, followed by a system that shows availability, captures the details your team needs and keeps reservations in one place.

The scope can range from a focused booking form on an existing website to a full booking and fleet or room management area. We agree the workflow, the user roles and the information collected before development begins. Payments, where included, are handled through a provider you choose and control.',
       'Car hire companies, guest houses, tour operators and service businesses that take bookings manually or across several channels.',
       'Booking workflow design, availability and reservation records, admin views for the team, responsive customer-facing pages and a clear handover with the scope agreed in advance.',
       'A description of how bookings are taken today, the details your team needs, any existing website or system, and who will manage bookings day to day.',
       'quote', 'Request a quote', 'Booking & Reservation Systems in Kenya | AlbaTech Solutions',
       'Custom booking and reservation systems for Kenyan car hire, accommodation and service businesses. Scope and price agreed before work starts.',
       'published', 0, 10
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Property & Rental Management Systems', 'property-rental-management-systems',
       'Landlord and tenant portals for rent records, statements, maintenance requests and occupancy.',
       'We build property management systems for landlords, caretakers and small property managers who currently track rent and maintenance in notebooks, spreadsheets and chat threads.

A typical build brings units, tenants, rent records and maintenance requests into one place, with separate views for the people managing the property and the people living in it. We start with the properties and records you already have, then agree which parts to digitise first. It is the same platform we are developing as our own early-access rental product, adapted to your portfolio.',
       'Landlords, property managers and caretakers managing a small to medium portfolio of residential or commercial units.',
       'Unit and tenant records, rent and payment history, statements, maintenance request tracking, occupancy views and role-based access based on the agreed scope.',
       'A list of properties and units, how rent and payments are recorded now, how maintenance requests reach you, and who needs access to which records.',
       'quote', 'Request a quote', 'Property & Rental Management Systems in Kenya | AlbaTech Solutions',
       'Landlord and tenant portals for Kenyan rentals: rent records, statements, maintenance requests and occupancy. Discuss a build for your portfolio.',
       'published', 0, 20
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Visitor & Gate Pass Management', 'visitor-gate-pass-management',
       'Digital gate passes and visitor logs for estates, offices and events, with approval at the gate.',
       'A visitor and gate pass system replaces the paper register at the gate with a record that can be checked, approved and searched. Guards or reception staff can issue or verify a pass, and the people expecting a visitor can approve access without a phone call.

We scope this as a pilot around your site first: the entry points, who approves visitors, what the guard needs to see and what records you want to keep. It can run alongside an existing access system or as a standalone web tool. We do not claim a deployed gate pass product; each implementation is quoted and built for the site.',
       'Estates, office buildings, schools, churches and event organisers that need a reliable visitor record and approval step.',
       'Visitor registration, pass issue and verification, host approval, entry and exit logs, searchable records and role-based access for guards and administrators.',
       'The number of entry points, how visitors are registered today, who approves access, and any devices already available at the gate.',
       'quote', 'Request a pilot quote', 'Visitor & Gate Pass Management in Kenya | AlbaTech Solutions',
       'Digital visitor registration and gate pass systems for Kenyan estates, offices and events. Pilot scope and price agreed before work starts.',
       'published', 0, 30
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'POS & Inventory Systems', 'pos-inventory-systems',
       'Point-of-sale and stock control for shops, restaurants and small distributors.',
       'We build point-of-sale and inventory tools for businesses that have outgrown a cash book or a single spreadsheet. The aim is to make sales, stock levels and reordering visible without adding a complicated system the team will not use.

Work usually starts with the products you sell, how stock arrives and how sales are recorded today. From there we agree the sales screen, receipt or record, stock movements and the reports the owner needs. Hardware, payment providers and any existing accounting tools are confirmed before the build starts.',
       'Shops, restaurants, pharmacies and small distributors that need clearer sales and stock records.',
       'Product and stock records, sales entry, stock movement history, low-stock views, simple reports and user roles based on the agreed scope.',
       'Your product list, how stock is received and counted, how sales are recorded now, and any receipt, payment or accounting requirements.',
       'quote', 'Request a quote', 'POS & Inventory Systems in Kenya | AlbaTech Solutions',
       'Custom point-of-sale and inventory systems for Kenyan shops, restaurants and distributors. Agree the workflow and price before development.',
       'published', 0, 40
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Custom Internal Tools', 'custom-internal-tools',
       'Small internal systems that replace repetitive spreadsheets, approvals and manual follow-up.',
       'Not every business needs a large platform. Sometimes the useful fix is a focused internal tool for one process that is currently held together by a spreadsheet, repeated messages and memory.

We map the process, identify where time is lost and build the smallest system that removes the repeated manual work. Examples include approval workflows, client or job tracking, simple dashboards and records that several people need to update safely. The scope is deliberately narrow so the tool is useful quickly and can grow only if it needs to.',
       'Small teams and business owners who run an important process through spreadsheets, email threads or paper.',
       'Process review, a focused internal tool, user roles, simple reporting and a handover that explains how the tool fits into daily work.',
       'A description of the process, the people involved, the records used today and the outcome you want to improve.',
       'quote', 'Request a quote', 'Custom Internal Tools for Kenyan Businesses | AlbaTech Solutions',
       'Focused internal tools that replace manual spreadsheets and approval chains for Kenyan businesses. Start with a practical process review.',
       'published', 0, 50
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO work_projects
(category_id, kind, title, slug, description, challenge, solution, features_json, result, stack_json, gallery_json, privacy_note, client_name, live_url, cover_image, lifecycle_status, platform, download_url, status, is_featured, sort_order, meta_title, meta_description)
SELECT id, 'project', 'Big Kahuna Car Hire', 'big-kahuna-car-hire',
       'A car hire website for Nairobi with a browsable fleet, an online booking flow and an admin area for managing vehicles and reservations.',
       'Big Kahuna needed a website that did more than list cars. Customers had to be able to see what was available and start a booking online, while the team needed a simple way to keep fleet and reservation details current.',
       'We built a responsive car hire site around the fleet and the booking journey. Visitors can compare vehicles by category and submit a booking request with the details the team needs to follow up. An admin area gives the team a single place to update vehicles and manage incoming reservations.',
       '["Fleet browsing by vehicle category","Online booking request flow","Admin area for vehicles and reservations","Responsive, mobile-first layout","Clear WhatsApp and contact calls to action"]',
       'A live booking-focused website that gives customers a direct route from browsing the fleet to submitting a reservation, and gives the team one place to manage incoming bookings.',
       '["Responsive web application","Admin dashboard"]',
       '["assets/images/work/big-kahuna-fleet.webp"]',
       NULL,
       'Big Kahuna Car Hire', 'https://bigkahunacarhire.co.ke', 'assets/images/work/big-kahuna-home.webp',
       'live', 'Web', NULL, 'published', 1, 10,
       'Big Kahuna Car Hire Case Study | AlbaTech Solutions',
       'How AlbaTech built a car hire website for Big Kahuna with fleet browsing, online booking and an admin area for reservations.'
FROM service_categories WHERE slug = 'web-design-digital-presence';

INSERT IGNORE INTO work_projects
(category_id, kind, title, slug, description, challenge, solution, features_json, result, stack_json, gallery_json, privacy_note, client_name, live_url, cover_image, lifecycle_status, platform, download_url, status, is_featured, sort_order, meta_title, meta_description)
SELECT id, 'product', 'Budget254', 'budget254',
       'An online toolkit that brings Kenyan PAYE, M-Pesa and everyday cost calculators into one place.',
       'Kenyan salary, tax and mobile-money costs are usually worked out across several sites, spreadsheets and rough estimates.',
       'Budget254 collects the calculators people reach for most into one simple site, starting with PAYE and M-Pesa costs, and keeps adding everyday money tools.',
       '["PAYE calculator for Kenyan salaries","M-Pesa transaction cost calculators","Mobile-friendly interface","One place for everyday cost checks"]',
       'A live public product that gives Kenyans a faster way to check common costs before making a decision.',
       NULL,
       '["assets/images/work/budget254-tools.webp"]',
       NULL,
       NULL, 'https://budget254.co.ke', 'assets/images/work/budget254-home.webp',
       'live', 'Web', NULL, 'published', 1, 20,
       'Budget254 | Kenyan PAYE & M-Pesa Calculators',
       'Budget254 is an AlbaTech product that puts Kenyan PAYE, M-Pesa and everyday cost calculators in one place, free to use online.'
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO work_projects
(category_id, kind, title, slug, description, challenge, solution, features_json, result, stack_json, gallery_json, privacy_note, client_name, live_url, cover_image, lifecycle_status, platform, download_url, status, is_featured, sort_order, meta_title, meta_description)
SELECT id, 'product', 'PesaLens', 'pesalens',
       'An Android app that reads M-Pesa messages on the phone and turns them into a clear record of spending.',
       'M-Pesa transaction messages hold a useful record of daily spending, but reading through them one by one makes it hard to see where money goes.',
       'PesaLens reads M-Pesa messages on the device and groups them into a spending view. The app is designed around privacy: message data stays on the phone and no M-Pesa PIN, password or one-time code is collected.',
       '["Expense summaries built from M-Pesa messages","On-device processing","No M-Pesa PIN or password collection","Android APK install"]',
       'A working beta for Android that gives users a private, phone-based view of their M-Pesa spending.',
       NULL,
       NULL,
       'PesaLens processes M-Pesa messages on your phone. Your messages and spending summaries stay on the device, and the app never asks for your M-Pesa PIN, password or one-time codes. The beta is distributed as an APK while it is being tested.',
       NULL, NULL, 'assets/images/work/pesalens.webp',
       'beta', 'Android (APK)', NULL, 'published', 1, 30,
       'PesaLens | M-Pesa Expense Tracking for Android',
       'PesaLens is an AlbaTech Android app that turns M-Pesa messages into a private spending record on your phone. Currently in beta.'
FROM service_categories WHERE slug = 'software-business-systems';

INSERT IGNORE INTO work_projects
(category_id, kind, title, slug, description, challenge, solution, features_json, result, stack_json, gallery_json, privacy_note, client_name, live_url, cover_image, lifecycle_status, platform, download_url, status, is_featured, sort_order, meta_title, meta_description)
SELECT id, 'product', 'Rental & Property Management', 'rental-property-management',
       'Landlord and tenant portals for Kenyan rentals, covering rent records, statements, maintenance requests and occupancy.',
       'Landlords and property managers often track rent, arrears, maintenance and tenant records across notebooks, WhatsApp threads and spreadsheets.',
       'We are building a web platform with separate landlord and tenant views: rent and payment records, statements, maintenance requests and a clear picture of occupancy. It is in early access with a small group of users before wider release.',
       '["Landlord dashboard","Tenant portal","Rent and payment records","Maintenance requests","Occupancy overview"]',
       'In early access. We are working with landlords and property managers to shape the first release.',
       NULL,
       NULL,
       'Tenant and payment information is only used to run the service. Early-access participants can ask to have their data removed.',
       NULL, NULL, 'assets/images/work/rental-system.webp',
       'early_access', 'Web', NULL, 'published', 1, 40,
       'Rental & Property Management System | Early Access',
       'A rental and property management platform in early access from AlbaTech, with landlord and tenant portals for Kenyan rentals.'
FROM service_categories WHERE slug = 'software-business-systems';
