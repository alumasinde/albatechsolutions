-- Phase 2: service URL aliases for safe canonical slug changes.
-- One legacy slug maps to one current service. A unique slug can never point
-- to more than one destination.

CREATE TABLE IF NOT EXISTS service_slug_aliases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    legacy_slug VARCHAR(200) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_service_slug_aliases_slug (legacy_slug),
    KEY idx_service_slug_aliases_service (service_id),
    CONSTRAINT fk_service_slug_aliases_service
        FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO service_slug_aliases (service_id, legacy_slug)
SELECT id, 'website-design-development' FROM services WHERE slug = 'website-design-kenya'
ON DUPLICATE KEY UPDATE service_id = VALUES(service_id);

INSERT INTO service_slug_aliases (service_id, legacy_slug)
SELECT id, 'cr12-applications' FROM services WHERE slug = 'cr12-application'
ON DUPLICATE KEY UPDATE service_id = VALUES(service_id);

INSERT INTO service_slug_aliases (service_id, legacy_slug)
SELECT id, 'networking' FROM services WHERE slug = 'wifi-networking'
ON DUPLICATE KEY UPDATE service_id = VALUES(service_id);
