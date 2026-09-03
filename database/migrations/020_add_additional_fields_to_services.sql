-- ==========================================================
-- Migration: 020_add_additional_fields_to_services.sql
-- Description: Add requirements, features and FAQs to services
-- ==========================================================

ALTER TABLE services
    ADD COLUMN requirements TEXT NULL
        AFTER description,
    ADD COLUMN features JSON NULL
        AFTER requirements,
    ADD COLUMN faqs JSON NULL
        AFTER features;