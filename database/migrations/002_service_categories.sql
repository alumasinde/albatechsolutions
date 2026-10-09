CREATE TABLE IF NOT EXISTS service_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    description TEXT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_service_categories_slug (slug),
    KEY idx_service_categories_status_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO service_categories (name, slug, description, status, sort_order) VALUES
('Web Design & Digital Presence', 'web-design-digital-presence', 'Practical websites and digital tools that help businesses establish and grow their online presence.', 'active', 10),
('Government & Business Assistance', 'government-business-assistance', 'Independent assistance and guidance with selected government and business processes in Kenya.', 'active', 20),
('Career Documents', 'career-documents', 'Practical help improving CVs and job application documents.', 'active', 30);
