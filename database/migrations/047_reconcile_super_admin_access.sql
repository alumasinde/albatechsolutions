-- Repair Super Admin authorization for existing installations.
-- The application also has an explicit Super Admin bypass in Auth::can(), but
-- keeping the database relationship complete preserves accurate role reports
-- and protects direct permission queries.

INSERT INTO roles (name, slug, description, is_system)
VALUES ('Super Admin', 'super-admin', 'Full unrestricted access', 1)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    is_system = 1,
    deleted_at = NULL;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;
