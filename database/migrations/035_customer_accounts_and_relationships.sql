-- Migration 035: customer account/relationship layer.
-- Customers use the existing customer role and authentication system.
-- Assistance requests remain usable without an account, but can be linked securely.

ALTER TABLE assistance_requests
    ADD COLUMN IF NOT EXISTS customer_user_id BIGINT UNSIGNED NULL;

CREATE INDEX idx_assistance_customer_user ON assistance_requests(customer_user_id, created_at);

ALTER TABLE assistance_requests
    ADD CONSTRAINT fk_assistance_customer_user
    FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE SET NULL;

INSERT INTO permissions (name, slug, module, description) VALUES
('View Customer Accounts', 'customers.view', 'customers', 'View customer-facing account information and relationships')
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug IN ('super-admin','admin') AND p.slug='customers.view'
ON DUPLICATE KEY UPDATE role_id = role_id;
