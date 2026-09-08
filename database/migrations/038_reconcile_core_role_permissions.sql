-- Reconcile core role permissions for existing installations.
-- This migration is intentionally idempotent and repairs databases that were
-- provisioned before the roles.view permission relationship was applied.

INSERT INTO permissions (name, slug, module, description) VALUES
    ('View Roles', 'roles.view', 'roles', 'View roles and permissions'),
    ('Manage Roles', 'roles.manage', 'roles', 'Create roles, assign permissions')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    module = VALUES(module),
    description = VALUES(description);

-- Super Admin retains unrestricted role access.
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.slug IN ('roles.view', 'roles.manage')
WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Admin may inspect roles but cannot change role permissions.
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.slug = 'roles.view'
WHERE r.slug = 'admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

DELETE rp
FROM role_permissions rp
INNER JOIN roles r ON r.id = rp.role_id
INNER JOIN permissions p ON p.id = rp.permission_id
WHERE r.slug = 'admin'
  AND p.slug = 'roles.manage';
