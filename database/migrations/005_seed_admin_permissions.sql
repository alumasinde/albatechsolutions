INSERT INTO permissions (name, slug, module, description) VALUES
    ('Manage Settings', 'settings.manage', 'settings', 'Edit branding, theme, contact, SEO defaults'),
    ('View Users', 'users.view', 'users', 'View staff/admin/customer accounts'),
    ('Manage Users', 'users.manage', 'users', 'Create, edit, deactivate accounts'),
    ('View Roles', 'roles.view', 'roles', 'View roles and permissions'),
    ('Manage Roles', 'roles.manage', 'roles', 'Create roles, assign permissions'),
    ('View Audit Log', 'audit.view', 'audit', 'View system activity/audit history'),
    ('Manage Media', 'media.manage', 'media', 'Upload and manage media library files')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Super Admin gets everything.
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Admin gets everything except role management (reserved for Super Admin).
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'admin' AND p.slug NOT IN ('roles.manage')
ON DUPLICATE KEY UPDATE role_id = role_id;
