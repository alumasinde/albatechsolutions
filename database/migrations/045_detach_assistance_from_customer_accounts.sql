-- Phase 2: detach the assistance workflow from retired customer accounts.
-- Fresh installs run migrations exactly once. The preceding migration chain
-- still contains the assistance_requests customer foreign key and index, so
-- standard broadly compatible MySQL/MariaDB syntax is sufficient.

ALTER TABLE assistance_requests
    DROP FOREIGN KEY fk_assistance_customer_user;

DROP INDEX idx_assistance_customer_user ON assistance_requests;

ALTER TABLE assistance_requests
    DROP COLUMN customer_user_id;

-- Request-level notification preferences are now the canonical preference source.
