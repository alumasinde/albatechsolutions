CREATE TABLE IF NOT EXISTS quote_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    business_name VARCHAR(180) NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(50) NULL,
    service_id BIGINT UNSIGNED NULL,
    budget VARCHAR(80) NULL,
    project_type VARCHAR(120) NULL,
    message TEXT NOT NULL,
    source VARCHAR(80) NOT NULL DEFAULT 'website',
    status ENUM('new','contacted','qualified','quote_sent','won','lost','spam') NOT NULL DEFAULT 'new',
    notes TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_quote_status (status),
    KEY idx_quote_created (created_at),
    KEY idx_quote_service (service_id),
    CONSTRAINT fk_quote_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    client_name VARCHAR(180) NULL,
    industry VARCHAR(120) NULL,
    location VARCHAR(120) NULL,
    summary VARCHAR(500) NOT NULL,
    description LONGTEXT NULL,
    challenge TEXT NULL,
    solution TEXT NULL,
    results TEXT NULL,
    technologies VARCHAR(500) NULL,
    image_path VARCHAR(500) NULL,
    project_url VARCHAR(500) NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    KEY idx_projects_status (status),
    KEY idx_projects_featured (featured),
    KEY idx_projects_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
    ('View Leads', 'leads.view', 'growth', 'View website quote requests and leads'),
    ('Manage Leads', 'leads.manage', 'growth', 'Update lead status and notes'),
    ('View Projects', 'projects.view', 'growth', 'View portfolio projects'),
    ('Manage Projects', 'projects.manage', 'growth', 'Create, edit, publish and delete portfolio projects')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'admin' AND p.module = 'growth'
ON DUPLICATE KEY UPDATE role_id = role_id;
