-- Migration 040: production query indexes.
-- This migration is intentionally append-only. The migration runner records the
-- filename after a successful execution, so the statements are not replayed.

ALTER TABLE users
    ADD INDEX idx_users_active_failed_login (is_active, failed_login_attempts, last_failed_login_at);

ALTER TABLE assistance_notifications
    ADD INDEX idx_assistance_notifications_retry (status, next_attempt_at);

ALTER TABLE growth_events
    ADD INDEX idx_growth_events_visitor_time (visitor_hash, occurred_at);

ALTER TABLE assistant_messages
    ADD INDEX idx_assistant_messages_direction_time (direction, created_at);
