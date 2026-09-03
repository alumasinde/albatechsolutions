-- Migration 031: AlbaTech assistance request engine.
-- Safe to re-run. Creates a separate workflow from legacy project quote requests.

CREATE TABLE IF NOT EXISTS assistance_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(32) NOT NULL,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(190) NULL,
    category VARCHAR(40) NOT NULL,
    service_id BIGINT UNSIGNED NULL,
    message TEXT NOT NULL,
    preferred_contact VARCHAR(20) NOT NULL DEFAULT 'whatsapp',
    status VARCHAR(30) NOT NULL DEFAULT 'new',
    assigned_to BIGINT UNSIGNED NULL,
    admin_notes TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    consent_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_request_number (request_number),
    KEY idx_assistance_status_created (status, created_at),
    KEY idx_assistance_category (category),
    KEY idx_assistance_service (service_id),
    KEY idx_assistance_assignee (assigned_to),
    CONSTRAINT fk_assistance_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    CONSTRAINT fk_assistance_assignee FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_request_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    from_status VARCHAR(30) NULL,
    to_status VARCHAR(30) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistance_history_request (assistance_request_id, created_at),
    CONSTRAINT fk_assistance_history_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_history_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('View Assistance Requests', 'assistance.view', 'assistance', 'View incoming digital assistance requests'),
('Manage Assistance Requests', 'assistance.manage', 'assistance', 'Update and manage digital assistance request workflow')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug IN ('assistance.view','assistance.manage')
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO menu_items (menu_id, label, url, sort_order, opens_new_tab)
SELECT m.id, 'Get Help', '/get-help', 1, 0
FROM menus m
WHERE m.slug = 'header'
  AND NOT EXISTS (SELECT 1 FROM menu_items mi WHERE mi.menu_id = m.id AND mi.url = '/get-help');
