-- Payments v2: provider-agnostic payment records with Paystack support.
-- Existing M-Pesa/manual records are preserved; legacy Daraja columns remain
-- nullable for historical data but are no longer used by the application.

ALTER TABLE payments
    DROP FOREIGN KEY fk_payments_order;

ALTER TABLE payments
    DROP INDEX uq_payments_checkout_request;

ALTER TABLE payments
    CHANGE order_id context_id BIGINT UNSIGNED NULL,
    MODIFY method VARCHAR(50) NOT NULL,
    MODIFY status VARCHAR(30) NOT NULL DEFAULT 'pending',
    ADD context_type VARCHAR(100) NULL AFTER id,
    ADD gateway VARCHAR(50) NULL AFTER context_id,
    ADD channel VARCHAR(50) NULL AFTER method,
    ADD reference VARCHAR(100) NULL AFTER currency,
    ADD gateway_transaction_id VARCHAR(100) NULL AFTER reference,
    ADD gateway_response VARCHAR(500) NULL AFTER gateway_transaction_id,
    ADD customer_email VARCHAR(255) NULL AFTER gateway_response,
    ADD customer_phone VARCHAR(30) NULL AFTER customer_email,
    ADD metadata JSON NULL AFTER customer_phone,
    ADD fulfilled_at DATETIME NULL AFTER verified_at,
    ADD UNIQUE KEY uq_payments_reference (reference),
    ADD KEY idx_payments_context (context_type, context_id),
    ADD KEY idx_payments_gateway_status (gateway, status);

UPDATE payments
SET context_type = 'order',
    gateway = CASE WHEN method = 'mpesa' THEN 'daraja' ELSE 'manual' END,
    reference = CONCAT('LEGACY-', id)
WHERE context_type IS NULL;

-- Keep the application-level data model generic. Old order records are still
-- represented as context_type=order/context_id=<orders.id>.
