-- 033: WhatsApp hardening.
-- * Deduplicate messages by Meta message id (webhook retries no longer double-insert).
-- * Track who sent each outbound message (customer / bot / agent / system), send errors,
--   retry state and downloaded media.
-- * Queue raw webhook events so the endpoint can answer Meta immediately.
-- * One row per contact for the 24-hour window, bot takeover, topic and source page.
-- * Proof-of-consent records for the enquiry form opt-in.

ALTER TABLE whatsapp_messages MODIFY wa_message_id VARCHAR(100) NULL DEFAULT NULL;
UPDATE whatsapp_messages SET wa_message_id = NULL WHERE wa_message_id = '';
DELETE m1 FROM whatsapp_messages m1
JOIN whatsapp_messages m2 ON m1.direction = m2.direction AND m1.wa_message_id = m2.wa_message_id AND m1.id > m2.id;

ALTER TABLE whatsapp_messages
    DROP INDEX idx_whatsapp_messages_wamid,
    ADD UNIQUE KEY uq_whatsapp_messages_wamid (direction, wa_message_id),
    ADD COLUMN origin VARCHAR(10) NOT NULL DEFAULT 'customer' AFTER direction,
    ADD COLUMN admin_user_id BIGINT UNSIGNED NULL AFTER origin,
    ADD COLUMN error_code VARCHAR(20) NULL AFTER status,
    ADD COLUMN error_message VARCHAR(500) NULL AFTER error_code,
    ADD COLUMN request_json MEDIUMTEXT NULL AFTER payload,
    ADD COLUMN attempts TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER request_json,
    ADD COLUMN next_attempt_at DATETIME NULL AFTER attempts,
    ADD COLUMN media_id VARCHAR(100) NULL AFTER next_attempt_at,
    ADD COLUMN media_path VARCHAR(255) NULL AFTER media_id,
    ADD COLUMN media_mime VARCHAR(100) NULL AFTER media_path,
    ADD KEY idx_whatsapp_messages_retry (status, next_attempt_at),
    ADD KEY idx_whatsapp_messages_origin (wa_number, origin, created_at);

UPDATE whatsapp_messages SET origin = 'system' WHERE direction = 'out';

CREATE TABLE IF NOT EXISTS whatsapp_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    payload MEDIUMTEXT NOT NULL,
    status VARCHAR(12) NOT NULL DEFAULT 'pending',
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    last_error VARCHAR(500) NULL,
    received_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_whatsapp_events_status (status, received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS whatsapp_contacts (
    wa_number VARCHAR(20) NOT NULL,
    contact_name VARCHAR(150) NOT NULL DEFAULT '',
    topic VARCHAR(20) NULL,
    source_page VARCHAR(255) NULL,
    last_inbound_at DATETIME NULL,
    bot_paused_until DATETIME NULL,
    menu_sent_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (wa_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO whatsapp_contacts (wa_number, contact_name, last_inbound_at)
SELECT wa_number, '', MAX(CASE WHEN direction = 'in' THEN created_at END)
FROM whatsapp_messages GROUP BY wa_number;

CREATE TABLE IF NOT EXISTS whatsapp_consents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    wa_number VARCHAR(20) NOT NULL,
    source VARCHAR(50) NOT NULL,
    reference VARCHAR(20) NULL,
    page VARCHAR(255) NULL,
    consent_text VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_whatsapp_consents_number (wa_number, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public) VALUES
('whatsapp_menu_enabled', '1', 'boolean', 'whatsapp', 1),
('whatsapp_business_hours', '', 'string', 'whatsapp', 1),
('whatsapp_after_hours_text', 'We are outside working hours right now. Leave your message and we will reply as soon as we open.', 'text', 'whatsapp', 1),
('whatsapp_reopen_template', '', 'string', 'whatsapp', 1),
('whatsapp_payload_retention_days', '30', 'integer', 'whatsapp', 1),
('whatsapp_media_retention_days', '30', 'integer', 'whatsapp', 1)
ON DUPLICATE KEY UPDATE setting_key = setting_key;
