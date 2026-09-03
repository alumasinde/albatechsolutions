INSERT INTO permissions (name, slug, module, description) VALUES
    ('View Pages', 'pages.view', 'cms', 'View CMS pages'),
    ('Manage Pages', 'pages.manage', 'cms', 'Create, edit, publish, delete pages'),
    ('View Blog', 'blog.view', 'cms', 'View blog posts and categories'),
    ('Manage Blog', 'blog.manage', 'cms', 'Create, edit, publish, delete blog posts'),
    ('Manage Menus', 'menus.manage', 'cms', 'Edit header/footer navigation'),
    ('Manage Banners', 'banners.manage', 'cms', 'Edit homepage and promotional banners'),
    ('Manage FAQs', 'faqs.manage', 'cms', 'Edit frequently asked questions'),
    ('Manage Testimonials', 'testimonials.manage', 'cms', 'Edit client testimonials')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'admin' AND p.module = 'cms'
ON DUPLICATE KEY UPDATE role_id = role_id;
