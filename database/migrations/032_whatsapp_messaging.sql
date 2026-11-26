CREATE TABLE IF NOT EXISTS whatsapp_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    direction ENUM('in', 'out') NOT NULL,
    wa_number VARCHAR(20) NOT NULL,
    contact_name VARCHAR(150) NOT NULL DEFAULT '',
    message_type VARCHAR(30) NOT NULL DEFAULT 'text',
    body TEXT NULL,
    wa_message_id VARCHAR(100) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT '',
    payload MEDIUMTEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_whatsapp_messages_number (wa_number, created_at),
    KEY idx_whatsapp_messages_wamid (wa_message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS whatsapp_optouts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    wa_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_optouts_number (wa_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO permissions (name, slug, description) VALUES
('Manage WhatsApp', 'whatsapp.manage', 'View the WhatsApp inbox and send messages');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'administrator' AND p.slug = 'whatsapp.manage';

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('whatsapp_ack_enabled', '1', 'boolean', 'whatsapp', 1),
('whatsapp_auto_reply_enabled', '1', 'boolean', 'whatsapp', 1),
('whatsapp_auto_reply_text', 'Thanks for contacting AlbaTech Solutions. We have received your message and will get back to you shortly. Reply STOP to opt out of messages.', 'text', 'whatsapp', 1),
('whatsapp_email_alerts', '1', 'boolean', 'whatsapp', 1),
('whatsapp_keyword_replies', '1', 'boolean', 'whatsapp', 1)
ON DUPLICATE KEY UPDATE setting_key = setting_key;
