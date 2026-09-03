-- Migration 038: AlbaTech Digital Assistant
CREATE TABLE IF NOT EXISTS assistant_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_token_hash CHAR(64) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    state JSON NULL,
    started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_activity_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    KEY idx_assistant_sessions_token (session_token_hash),
    KEY idx_assistant_sessions_user (user_id),
    KEY idx_assistant_sessions_activity (last_activity_at),
    CONSTRAINT fk_assistant_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistant_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id BIGINT UNSIGNED NOT NULL,
    direction ENUM('user','assistant','system') NOT NULL,
    message TEXT NOT NULL,
    metadata JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistant_messages_session (session_id, created_at),
    CONSTRAINT fk_assistant_messages_session FOREIGN KEY (session_id) REFERENCES assistant_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistant_service_matches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(8,4) NOT NULL DEFAULT 0,
    reason VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistant_match (session_id, service_id),
    KEY idx_assistant_match_score (session_id, score),
    CONSTRAINT fk_assistant_match_session FOREIGN KEY (session_id) REFERENCES assistant_sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistant_match_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('View Assistant Sessions', 'assistant.sessions.view', 'assistant', 'View digital assistant conversations and service matches'),
('Manage Assistant', 'assistant.manage', 'assistant', 'Manage digital assistant settings and handoffs')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug IN ('assistant.sessions.view','assistant.manage')
ON DUPLICATE KEY UPDATE role_id=role_id;
