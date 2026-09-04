-- Phase 2: detach the assistance workflow from retired customer accounts.
-- Request-level preferences remain supported. Historical customer IDs are retained
-- until a future major data migration explicitly removes the account subsystem.

ALTER TABLE assistance_requests
    DROP FOREIGN KEY IF EXISTS fk_assistance_customer_user;

DROP INDEX IF EXISTS idx_assistance_customer_user ON assistance_requests;

ALTER TABLE assistance_requests
    DROP COLUMN IF EXISTS customer_user_id;

-- Request-level notification preferences are now the canonical preference source.
