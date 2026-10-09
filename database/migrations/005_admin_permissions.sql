INSERT IGNORE INTO permissions (name, slug, description) VALUES
('Manage services', 'services.manage', 'Create, edit, publish and feature services'),
('Manage categories', 'categories.manage', 'Create and edit service categories'),
('Manage site settings', 'settings.manage', 'Update public website and SEO settings');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'administrator'
  AND p.slug IN ('services.manage', 'categories.manage', 'settings.manage');
