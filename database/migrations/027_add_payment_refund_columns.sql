ALTER TABLE payments
    ADD refunded_at DATETIME NULL AFTER fulfilled_at,
    ADD refunded_by BIGINT UNSIGNED NULL AFTER refunded_at,
    ADD refund_reason VARCHAR(500) NULL AFTER refunded_by,
    ADD CONSTRAINT fk_payments_refunded_by FOREIGN KEY (refunded_by) REFERENCES users(id) ON DELETE SET NULL;

INSERT INTO permissions (name, slug, module, description) VALUES
    ('Refund Payments', 'payments.refund', 'orders', 'Cancel a paid order and record/process its refund')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'super-admin' AND p.slug = 'payments.refund'
ON DUPLICATE KEY UPDATE role_id = role_id;
