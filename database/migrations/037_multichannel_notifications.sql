-- Migration 037: multi-channel notification delivery, preferences, templates and retries.
-- Re-runnable by the migration runner.

ALTER TABLE assistance_notifications
    ADD COLUMN IF NOT EXISTS attempt_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS next_attempt_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS last_attempt_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS channel_message_id VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS template_name VARCHAR(120) NULL,
    ADD COLUMN IF NOT EXISTS template_language VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS body TEXT NULL,
    ADD COLUMN IF NOT EXISTS template_data JSON NULL;

CREATE INDEX idx_assistance_notification_retry
    ON assistance_notifications(status, next_attempt_at, attempt_count);

CREATE TABLE IF NOT EXISTS assistance_notification_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    email_enabled TINYINT(1) NOT NULL DEFAULT 1,
    sms_enabled TINYINT(1) NOT NULL DEFAULT 1,
    whatsapp_enabled TINYINT(1) NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_notification_pref_request (assistance_request_id),
    KEY idx_assistance_notification_pref_user (user_id),
    CONSTRAINT fk_assistance_notification_pref_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_notification_pref_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS customer_notification_preferences (
    user_id BIGINT UNSIGNED PRIMARY KEY,
    email_enabled TINYINT(1) NOT NULL DEFAULT 1,
    sms_enabled TINYINT(1) NOT NULL DEFAULT 1,
    whatsapp_enabled TINYINT(1) NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_customer_notification_pref_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_notification_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_id BIGINT UNSIGNED NOT NULL,
    attempt_number SMALLINT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL,
    provider_message_id VARCHAR(255) NULL,
    error_message VARCHAR(1000) NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistance_notification_attempt_notification (notification_id, attempted_at),
    CONSTRAINT fk_assistance_notification_attempt_notification FOREIGN KEY (notification_id) REFERENCES assistance_notifications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_notification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(60) NOT NULL,
    channel VARCHAR(20) NOT NULL,
    template_name VARCHAR(120) NULL,
    language VARCHAR(20) NULL,
    subject_template VARCHAR(255) NULL,
    body_template TEXT NOT NULL,
    enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_notification_template (event, channel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO assistance_notification_preferences (assistance_request_id, user_id)
SELECT ar.id, ar.customer_user_id
FROM assistance_requests ar
LEFT JOIN assistance_notification_preferences p ON p.assistance_request_id = ar.id
WHERE p.id IS NULL;

INSERT INTO permissions (name, slug, module, description) VALUES
('Manage Assistance Notifications', 'assistance.notifications.manage', 'assistance', 'Manage notification preferences, retries and channel configuration for assistance requests')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug = 'assistance.notifications.manage'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO assistance_notification_templates (event, channel, template_name, language, subject_template, body_template) VALUES
('request_received','email',NULL,NULL,'We received your AlbaTech request — {{reference}}','Hello {{name}},\n\nWe received your AlbaTech digital assistance request {{reference}}.\n\nWe will review it and contact you using your preferred contact method.\n\nPlease never send your M-Pesa PIN, OTP or password to us.'),
('quote_sent','email',NULL,NULL,'Your AlbaTech quote is ready — {{quote_number}}','Hello {{name}},\n\nYour AlbaTech assistance quote {{quote_number}} is ready.\n\nReview your secure quote: {{url}}'),
('payment_verified','email',NULL,NULL,'Payment verified — {{quote_number}}','Hello {{name}},\n\nYour payment for {{quote_number}} has been verified.\n\nReceipt: {{url}}'),
('work_completed','email',NULL,NULL,'Your AlbaTech assistance is complete','Hello {{name}},\n\nYour assistance request {{reference}} has been completed.\n\n{{note}}\n\nView your request: {{url}}'),
('request_received','sms',NULL,NULL,NULL,'AlbaTech: We received request {{reference}}. We will contact you soon. Ref: {{reference}}.'),
('quote_sent','sms',NULL,NULL,NULL,'AlbaTech: Quote {{quote_number}} is ready. Review: {{url}}'),
('payment_verified','sms',NULL,NULL,NULL,'AlbaTech: Payment for {{quote_number}} has been verified. Receipt: {{url}}'),
('work_completed','sms',NULL,NULL,NULL,'AlbaTech: Request {{reference}} is complete. Thank you.'),
('request_received','whatsapp',NULL,'en_US',NULL,'Your AlbaTech request {{reference}} has been received.'),
('quote_sent','whatsapp',NULL,'en_US',NULL,'Your AlbaTech quote {{quote_number}} is ready. Review it here: {{url}}'),
('payment_verified','whatsapp',NULL,'en_US',NULL,'Your payment for {{quote_number}} has been verified. Receipt: {{url}}'),
('work_completed','whatsapp',NULL,'en_US',NULL,'Your AlbaTech request {{reference}} is complete. Thank you.')
ON DUPLICATE KEY UPDATE body_template = VALUES(body_template), subject_template = VALUES(subject_template);

INSERT INTO assistance_notification_templates (event, channel, template_name, language, subject_template, body_template) VALUES
('quote_accepted','email',NULL,NULL,'Quote accepted — {{quote_number}}','Hello {{name}},\n\nThanks for accepting quote {{quote_number}}. You can use your secure quote link to continue payment.'),
('payment_submitted','email',NULL,NULL,'Payment received for verification — {{quote_number}}','Hello {{name}},\n\nWe received your payment submission for {{quote_number}}. Your payment is being verified.'),
('payment_rejected','email',NULL,NULL,'Payment needs attention — {{quote_number}}','Hello {{name}},\n\nWe could not verify the payment for {{quote_number}}. Reason: {{reason}}'),
('work_assigned','email',NULL,NULL,'Your AlbaTech assistance is being prepared','Hello {{name}},\n\nYour assistance request {{reference}} has been assigned. Track progress: {{url}}'),
('work_started','email',NULL,NULL,'Work has started — {{reference}}','Hello {{name}},\n\nWork has started on your AlbaTech assistance request {{reference}}. Track progress: {{url}}'),
('progress_update','email',NULL,NULL,'Update on your AlbaTech request — {{reference}}','Hello {{name}},\n\n{{message}}\n\nTrack progress: {{url}}'),
('quote_accepted','sms',NULL,NULL,NULL,'AlbaTech: Quote {{quote_number}} accepted. You can continue with payment using your secure quote link.'),
('payment_submitted','sms',NULL,NULL,NULL,'AlbaTech: Payment for {{quote_number}} was submitted and is being verified.'),
('payment_rejected','sms',NULL,NULL,NULL,'AlbaTech: Payment for {{quote_number}} needs attention. Reason: {{reason}}'),
('work_assigned','sms',NULL,NULL,NULL,'AlbaTech: Request {{reference}} has been assigned. Track: {{url}}'),
('work_started','sms',NULL,NULL,NULL,'AlbaTech: Work has started on {{reference}}. Track: {{url}}'),
('progress_update','sms',NULL,NULL,NULL,'AlbaTech update {{reference}}: {{message}}'),
('quote_accepted','whatsapp',NULL,'en_US',NULL,'Quote {{quote_number}} has been accepted. Continue with payment using your secure quote link.'),
('payment_submitted','whatsapp',NULL,'en_US',NULL,'Payment for {{quote_number}} was submitted and is being verified.'),
('payment_rejected','whatsapp',NULL,'en_US',NULL,'Payment for {{quote_number}} needs attention. Reason: {{reason}}'),
('work_assigned','whatsapp',NULL,'en_US',NULL,'Your AlbaTech request {{reference}} has been assigned. Track: {{url}}'),
('work_started','whatsapp',NULL,'en_US',NULL,'Work has started on {{reference}}. Track: {{url}}'),
('progress_update','whatsapp',NULL,'en_US',NULL,'Update for {{reference}}: {{message}}')
ON DUPLICATE KEY UPDATE body_template = VALUES(body_template), subject_template = VALUES(subject_template);
