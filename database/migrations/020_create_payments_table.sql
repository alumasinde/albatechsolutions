CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    method ENUM('mpesa', 'bank_transfer', 'manual') NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'rejected') NOT NULL DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'KES',

    -- M-Pesa STK Push specific
    phone_number VARCHAR(20) NULL,
    checkout_request_id VARCHAR(100) NULL,
    merchant_request_id VARCHAR(100) NULL,
    mpesa_receipt VARCHAR(50) NULL,
    result_desc VARCHAR(255) NULL,

    -- Bank transfer specific
    proof_path VARCHAR(500) NULL COMMENT 'private, outside web root',
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_payments_checkout_request (checkout_request_id),
    KEY idx_payments_order (order_id),
    KEY idx_payments_status (status),
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_verifier FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (name, slug, module, description) VALUES
    ('Manage Payments', 'payments.manage', 'orders', 'Verify bank transfer proofs, view payment records')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin' AND p.slug = 'payments.manage'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'admin' AND p.slug = 'payments.manage'
ON DUPLICATE KEY UPDATE role_id = role_id;
