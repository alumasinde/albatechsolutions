-- Migration 036: commercial/operational service catalogue.
-- Keeps core CMS service records stable while adding structured service commerce data.

CREATE TABLE IF NOT EXISTS service_commerce (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    pricing_mode ENUM('fixed','starting_from','quote','free') NOT NULL DEFAULT 'quote',
    customer_fee DECIMAL(12,2) NULL,
    government_fee_note VARCHAR(500) NULL,
    fee_disclaimer VARCHAR(500) NULL,
    turnaround_min_days SMALLINT UNSIGNED NULL,
    turnaround_max_days SMALLINT UNSIGNED NULL,
    requires_quote TINYINT(1) NOT NULL DEFAULT 1,
    instant_request TINYINT(1) NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    requirements JSON NULL,
    intake_questions JSON NULL,
    related_service_ids JSON NULL,
    internal_notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_service_commerce_service (service_id),
    KEY idx_service_commerce_active (active),
    CONSTRAINT fk_service_commerce_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE assistance_requests
    ADD COLUMN IF NOT EXISTS intake_answers JSON NULL AFTER message;

INSERT INTO permissions (name, slug, module, description) VALUES
('Manage Service Commerce', 'services.commerce.manage', 'services', 'Manage service pricing, requirements, intake questions and operational rules')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug='services.commerce.manage'
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Seed operational records for all currently published services without overwriting existing configuration.
INSERT INTO service_commerce (service_id, pricing_mode, customer_fee, requires_quote, instant_request, requirements, intake_questions, related_service_ids)
SELECT s.id,
       CASE s.price_type WHEN 'fixed' THEN 'fixed' WHEN 'starting_from' THEN 'starting_from' ELSE 'quote' END,
       s.price,
       CASE WHEN s.price_type = 'quote' THEN 1 ELSE 0 END,
       CASE WHEN s.price_type = 'fixed' THEN 1 ELSE 0 END,
       COALESCE(s.requirements, JSON_ARRAY()),
       JSON_ARRAY(),
       JSON_ARRAY()
FROM services s
LEFT JOIN service_commerce sc ON sc.service_id = s.id
WHERE sc.id IS NULL;
