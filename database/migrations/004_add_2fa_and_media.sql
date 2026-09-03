ALTER TABLE users
    ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER remember_token,
    ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER two_factor_secret,
    ADD COLUMN two_factor_recovery_codes TEXT NULL AFTER two_factor_enabled,
    ADD COLUMN two_factor_confirmed_at DATETIME NULL AFTER two_factor_recovery_codes;

CREATE TABLE IF NOT EXISTS media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploaded_by BIGINT UNSIGNED NULL,
    disk_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes INT UNSIGNED NOT NULL,
    purpose VARCHAR(100) NULL COMMENT 'e.g. logo, blog_cover, service_image',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    KEY idx_media_purpose (purpose),
    CONSTRAINT fk_media_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
