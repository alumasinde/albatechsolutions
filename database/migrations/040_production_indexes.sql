-- Migration 040: production query indexes.
-- This migration is intentionally append-only. The migration runner records the
-- filename after a successful execution, so the statements are not replayed.

ALTER TABLE users
    ADD INDEX idx_users_active_failed_login (is_active, failed_login_attempts, last_failed_login_at);

ALTER TABLE assistance_notifications
    ADD INDEX idx_assistance_notifications_retry (status, next_attempt_at);
