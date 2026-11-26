-- Replace em dashes in previously seeded copy. Statements are idempotent and only
-- touch rows that still contain the original seeded text, so admin edits are safe.

UPDATE site_settings
SET setting_value = 'Nationwide, fully online'
WHERE setting_key = 'service_area' AND setting_value = 'Nationwide — fully online';

UPDATE guides
SET content_html = REPLACE(
    REPLACE(content_html, 'page speed—not just', 'page speed, not just'),
    'should take—call', 'should take: call'
)
WHERE content_html LIKE '%—%';
