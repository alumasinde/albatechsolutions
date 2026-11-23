-- 024: Rebind services.category_id to the current schema, then re-seed the five
-- systems services from 022. On environments where services was copied from
-- another schema, fk_services_category still pointed at that other schema, so
-- the 022 INSERT ... SELECT statements failed with error 1452 and were ignored.

ALTER TABLE services DROP FOREIGN KEY fk_services_category;
ALTER TABLE services ADD CONSTRAINT fk_services_category
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT;

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
