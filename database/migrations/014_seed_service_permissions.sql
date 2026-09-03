INSERT INTO permissions (name, slug, module, description) VALUES
    ('View Services', 'services.view', 'cms', 'View the service catalogue'),
    ('Manage Services', 'services.manage', 'cms', 'Create, edit, publish, delete services')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'admin' AND p.slug IN ('services.view', 'services.manage')
ON DUPLICATE KEY UPDATE role_id = role_id;
