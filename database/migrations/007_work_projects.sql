CREATE TABLE IF NOT EXISTS work_projects (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    client_name VARCHAR(180) NULL,
    live_url VARCHAR(500) NULL,
    cover_image VARCHAR(500) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_work_projects_slug (slug),
    KEY idx_work_projects_public (status, is_featured, sort_order),
    KEY idx_work_projects_category (category_id, status, sort_order),
    CONSTRAINT fk_work_projects_category FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO permissions (name, slug, description) VALUES
('Manage portfolio projects', 'work.manage', 'Create, edit, publish, feature and order portfolio projects');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'administrator' AND p.slug = 'work.manage';
