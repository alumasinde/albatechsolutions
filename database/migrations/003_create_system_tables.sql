CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(150) NOT NULL,
    entity_type VARCHAR(100) NULL,
    entity_id BIGINT UNSIGNED NULL,
    meta JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_audit_user (user_id),
    KEY idx_audit_entity (entity_type, entity_id),
    KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limits (
    `key` VARCHAR(190) NOT NULL PRIMARY KEY,
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(190) NOT NULL PRIMARY KEY,
    `value` LONGTEXT NULL,
    `type` VARCHAR(20) NOT NULL DEFAULT 'string',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed baseline branding/theme settings so the admin panel has something to edit.
INSERT INTO settings (`key`, `value`, `type`) VALUES
    ('site_name', 'AlbaTech Solutions', 'string'),
    ('site_tagline', 'Technology & Government Services', 'string'),
    ('theme_color_primary', '#0f766e', 'string'),
    ('theme_color_secondary', '#1e293b', 'string'),
    ('theme_color_accent', '#f59e0b', 'string'),
    ('theme_font_family', 'Inter, sans-serif', 'string'),
    ('theme_radius', '10px', 'string'),
    ('contact_email', 'info@albatechsolutions.co.ke', 'string'),
    ('contact_phone', '', 'string'),
    ('seo_default_title', 'AlbaTech Solutions', 'string'),
    ('seo_default_description', '', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
