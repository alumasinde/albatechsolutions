-- The services table already defines is_featured in migration 013.
-- Keep this historical migration as the feature-seeding step only.

UPDATE services s
INNER JOIN (
    SELECT MIN(id) AS id
    FROM services
    WHERE status = 'published' AND deleted_at IS NULL
    GROUP BY category_id
) first_per_category ON first_per_category.id = s.id
SET s.is_featured = 1;
