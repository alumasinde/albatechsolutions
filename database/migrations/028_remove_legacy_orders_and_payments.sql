-- Phase 8: decommission the old authenticated order/checkout workflow.
-- The public site is now WhatsApp-first. Historical migrations are intentionally
-- left untouched so existing migration history remains reproducible.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_documents;
DROP TABLE IF EXISTS order_status_history;
DROP TABLE IF EXISTS orders;

SET FOREIGN_KEY_CHECKS = 1;

-- Remove permissions that only existed for the retired checkout workflow.
DELETE FROM role_permissions
WHERE permission_id IN (
    SELECT id FROM permissions WHERE slug IN ('orders.view', 'orders.manage', 'payments.manage', 'payments.refund')
);

DELETE FROM permissions
WHERE slug IN ('orders.view', 'orders.manage', 'payments.manage', 'payments.refund');
