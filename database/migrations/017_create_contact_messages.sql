CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'responded') NOT NULL DEFAULT 'new',
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_contact_messages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('whatsapp_number', '', 'string')
ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO permissions (name, slug, module, description) VALUES
    ('View Contact Messages', 'contact_messages.view', 'cms', 'View messages submitted through the public contact form')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin' AND p.slug = 'contact_messages.view'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'admin' AND p.slug = 'contact_messages.view'
ON DUPLICATE KEY UPDATE role_id = role_id;
