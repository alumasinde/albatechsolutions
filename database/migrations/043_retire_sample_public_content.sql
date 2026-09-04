-- Phase 2: clean retired/sample content from the active data model.
-- Historical tables remain untouched for deployment compatibility; the public
-- application no longer depends on banners, testimonials or editable menus.

UPDATE testimonials SET is_active = 0 WHERE deleted_at IS NULL;

-- The public layout uses fixed conversion navigation. Existing menu records
-- are retained for historical/admin compatibility but are no longer required
-- by the public rendering path.
