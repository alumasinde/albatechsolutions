-- 027: Optional turnaround text per service (e.g. "Same day", "2-4 working days").
-- Shown next to the price on service cards and pages; hidden when empty.

ALTER TABLE services ADD COLUMN turnaround VARCHAR(80) NULL AFTER pricing_label;
