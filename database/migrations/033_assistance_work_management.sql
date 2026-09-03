-- Migration 033: customer portal, work management, updates and reviews.
-- Re-runnable by the project's migration runner.

ALTER TABLE assistance_requests
    ADD COLUMN IF NOT EXISTS customer_token_hash CHAR(64) NULL,
    ADD COLUMN IF NOT EXISTS customer_token_encrypted TEXT NULL,
    ADD COLUMN IF NOT EXISTS assigned_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS due_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS started_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS completed_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS completion_note TEXT NULL;

CREATE INDEX idx_assistance_customer_token ON assistance_requests(customer_token_hash);

CREATE TABLE IF NOT EXISTS assistance_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NULL,
    status VARCHAR(25) NOT NULL DEFAULT 'pending',
    priority VARCHAR(15) NOT NULL DEFAULT 'normal',
    assigned_to BIGINT UNSIGNED NULL,
    due_at DATETIME NULL,
    completed_at DATETIME NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_assistance_task_request (assistance_request_id, sort_order),
    KEY idx_assistance_task_assignee (assigned_to, status),
    CONSTRAINT fk_assistance_task_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_task_assignee FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_assistance_task_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_updates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    author_id BIGINT UNSIGNED NULL,
    visibility VARCHAR(20) NOT NULL DEFAULT 'customer',
    update_type VARCHAR(30) NOT NULL DEFAULT 'progress',
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistance_update_request (assistance_request_id, created_at),
    CONSTRAINT fk_assistance_update_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_update_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    public_token_hash CHAR(64) NOT NULL,
    public_token_encrypted TEXT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment VARCHAR(1000) NULL,
    customer_name VARCHAR(120) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    reviewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    moderated_by BIGINT UNSIGNED NULL,
    moderated_at DATETIME NULL,
    moderation_note VARCHAR(500) NULL,
    UNIQUE KEY uq_assistance_review_request (assistance_request_id),
    UNIQUE KEY uq_assistance_review_token (public_token_hash),
    KEY idx_assistance_review_status (status),
    CONSTRAINT fk_assistance_review_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_review_moderator FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('Manage Assistance Work', 'assistance.work.manage', 'assistance', 'Assign, schedule and manage paid assistance work'),
('Manage Assistance Reviews', 'assistance.reviews.manage', 'assistance', 'Moderate customer reviews from completed assistance work')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug IN ('assistance.work.manage','assistance.reviews.manage')
ON DUPLICATE KEY UPDATE role_id = role_id;
