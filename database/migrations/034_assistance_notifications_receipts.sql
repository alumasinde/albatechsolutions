-- Migration 034: assistance notifications, receipt links and delivery history.
-- Re-runnable by the project's migration runner.

ALTER TABLE assistance_payments
    ADD COLUMN IF NOT EXISTS receipt_token_hash CHAR(64) NULL,
    ADD COLUMN IF NOT EXISTS receipt_token_encrypted TEXT NULL,
    ADD COLUMN IF NOT EXISTS receipt_issued_at DATETIME NULL;

CREATE UNIQUE INDEX uq_assistance_payment_receipt_token
    ON assistance_payments(receipt_token_hash);

CREATE TABLE IF NOT EXISTS assistance_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    channel VARCHAR(20) NOT NULL DEFAULT 'email',
    event VARCHAR(50) NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'queued',
    provider_message TEXT NULL,
    source_type VARCHAR(40) NULL,
    source_id BIGINT UNSIGNED NULL,
    sent_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_notification_event (assistance_request_id, channel, event, source_type, source_id),
    KEY idx_assistance_notification_request (assistance_request_id, created_at),
    KEY idx_assistance_notification_status (status),
    CONSTRAINT fk_assistance_notification_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('View Assistance Notifications', 'assistance.notifications.view', 'assistance', 'View delivery history for customer assistance notifications')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug = 'assistance.notifications.view'
ON DUPLICATE KEY UPDATE role_id = role_id;
