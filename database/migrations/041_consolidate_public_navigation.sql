-- V4.1 public navigation consolidation.
-- Safe to re-run: the public header is intentionally a fixed, concise
-- conversion-oriented navigation rather than a duplicate CMS CTA system.

SET @header_id = (SELECT id FROM menus WHERE slug = 'header' LIMIT 1);

DELETE FROM menu_items WHERE menu_id=@header_id;

INSERT INTO menu_items (menu_id, label, url, sort_order, opens_new_tab) VALUES
(@header_id, 'Home', '/', 1, 0),
(@header_id, 'Services', '/services', 2, 0),
(@header_id, 'Guides', '/blog', 3, 0),
(@header_id, 'FAQs', '/faqs', 4, 0),
(@header_id, 'About', '/about', 5, 0),
(@header_id, 'Contact', '/contact', 6, 0);

-- Get Assistance is deliberately rendered by the public layout so it cannot
-- be duplicated by CMS menu edits. The Digital Assistant remains accessible
-- directly but is not a primary navigation item.
