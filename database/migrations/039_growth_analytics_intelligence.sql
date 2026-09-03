-- Migration 039: first-party growth analytics and SEO/content intelligence
CREATE TABLE IF NOT EXISTS growth_page_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visitor_hash CHAR(64) NOT NULL,
    path VARCHAR(500) NOT NULL,
    page_type VARCHAR(40) NULL,
    entity_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NULL,
    referrer_host VARCHAR(255) NULL,
    utm_source VARCHAR(120) NULL,
    utm_medium VARCHAR(120) NULL,
    utm_campaign VARCHAR(180) NULL,
    viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_growth_views_time (viewed_at), KEY idx_growth_views_path_time (path, viewed_at),
    KEY idx_growth_views_visitor_time (visitor_hash, viewed_at), KEY idx_growth_views_source_time (utm_source, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS growth_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visitor_hash CHAR(64) NOT NULL,
    event_name VARCHAR(80) NOT NULL,
    path VARCHAR(500) NULL,
    service_id BIGINT UNSIGNED NULL,
    entity_id BIGINT UNSIGNED NULL,
    metadata JSON NULL,
    occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_growth_events_time (occurred_at), KEY idx_growth_events_name_time (event_name, occurred_at),
    KEY idx_growth_events_service_time (service_id, occurred_at),
    CONSTRAINT fk_growth_events_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS growth_content_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('page','service','blog') NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    note_type VARCHAR(60) NOT NULL,
    priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    note TEXT NOT NULL,
    status ENUM('open','done','dismissed') NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_growth_content_note (content_type, entity_id, note_type), KEY idx_growth_content_notes_status (status, priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('View Growth Intelligence', 'growth.intelligence.view', 'growth', 'View first-party analytics, conversion funnels and SEO/content intelligence'),
('Manage Growth Intelligence', 'growth.intelligence.manage', 'growth', 'Manage growth intelligence notes and recommendations')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug IN ('growth.intelligence.view','growth.intelligence.manage')
ON DUPLICATE KEY UPDATE role_id=role_id;
