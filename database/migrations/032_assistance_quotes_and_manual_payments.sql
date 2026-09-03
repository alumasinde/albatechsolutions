-- Migration 032: assistance quotes + manual payment confirmation.
-- Independent from the retired orders/payments checkout subsystem.

CREATE TABLE IF NOT EXISTS assistance_quotes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assistance_request_id BIGINT UNSIGNED NOT NULL,
    quote_number VARCHAR(40) NOT NULL,
    public_token_hash CHAR(64) NOT NULL,
    public_token_encrypted TEXT NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'KES',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    note TEXT NULL,
    expires_at DATETIME NULL,
    sent_at DATETIME NULL,
    accepted_at DATETIME NULL,
    paid_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_quote_number (quote_number),
    UNIQUE KEY uq_assistance_quote_token (public_token_hash),
    KEY idx_assistance_quote_request (assistance_request_id),
    KEY idx_assistance_quote_status (status),
    CONSTRAINT fk_assistance_quote_request FOREIGN KEY (assistance_request_id) REFERENCES assistance_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_quote_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_quote_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quote_id BIGINT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(12,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistance_quote_items_quote (quote_id, sort_order),
    CONSTRAINT fk_assistance_quote_items_quote FOREIGN KEY (quote_id) REFERENCES assistance_quotes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_quote_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quote_id BIGINT UNSIGNED NOT NULL,
    event VARCHAR(40) NOT NULL,
    actor_type VARCHAR(20) NOT NULL DEFAULT 'admin',
    actor_id BIGINT UNSIGNED NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_assistance_quote_events_quote (quote_id, created_at),
    CONSTRAINT fk_assistance_quote_events_quote FOREIGN KEY (quote_id) REFERENCES assistance_quotes(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_quote_events_user FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistance_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quote_id BIGINT UNSIGNED NOT NULL,
    payment_reference VARCHAR(50) NOT NULL,
    method VARCHAR(30) NOT NULL DEFAULT 'mpesa',
    amount DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'KES',
    mpesa_receipt VARCHAR(80) NULL,
    payer_phone VARCHAR(30) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'submitted',
    customer_note VARCHAR(500) NULL,
    admin_note VARCHAR(500) NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assistance_payment_reference (payment_reference),
    KEY idx_assistance_payment_quote (quote_id, created_at),
    KEY idx_assistance_payment_status (status),
    CONSTRAINT fk_assistance_payment_quote FOREIGN KEY (quote_id) REFERENCES assistance_quotes(id) ON DELETE CASCADE,
    CONSTRAINT fk_assistance_payment_verifier FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
('Manage Assistance Quotes', 'assistance.quotes.manage', 'assistance', 'Create, send and manage digital assistance quotes'),
('Manage Assistance Payments', 'assistance.payments.manage', 'assistance', 'Verify manual payments submitted against assistance quotes')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug IN ('assistance.quotes.manage','assistance.payments.manage')
ON DUPLICATE KEY UPDATE role_id = role_id;
