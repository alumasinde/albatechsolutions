-- Generic token table for password resets and email verification.
-- Raw tokens are never stored — only a SHA-256 hash, matching how the
-- app already treats secrets elsewhere (session tokens, CSRF tokens).
CREATE TABLE IF NOT EXISTS user_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(30) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_tokens_hash (token_hash),
    KEY idx_user_tokens_user_type (user_id, type),
    CONSTRAINT fk_user_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- email_verified_at already exists on users (migration 001) but has never
-- been set by any code path — nothing to alter, just start using it.
