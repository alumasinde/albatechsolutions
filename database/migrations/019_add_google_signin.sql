ALTER TABLE users
    MODIFY password VARCHAR(255) NULL COMMENT 'nullable for Google-only accounts',
    ADD COLUMN google_id VARCHAR(255) NULL AFTER password,
    ADD UNIQUE KEY uq_users_google_id (google_id);
