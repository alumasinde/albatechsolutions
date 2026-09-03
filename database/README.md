# Fixed database folder

This folder contains the audited migration chain for AlbaTech Solutions.

## Fixes

- `016_add_service_featured_flag.sql` no longer adds `is_featured`, because migration 013 already creates that column. It retains the intended featured-service seed/update.
- `021_add_missing_service_columns.sql` is now an intentional no-op because migration 020 already creates `features`.
- Historical migration filenames were preserved. Do not rename already-deployed migrations merely to remove the duplicate numeric `020` prefix; the runner tracks migration identity by filename.
- Migration `028_remove_legacy_orders_and_payments.sql` remains the controlled cleanup migration for the retired checkout/payment schema.

## Expected fresh-install result

Migrations 001–028 should execute without the duplicate-column failures encountered previously. The runner may still report the historical duplicate numeric prefix `020`; that is a warning about ordering labels, not a duplicate migration identity. The two migration files have different filenames and are tracked independently.

After a clean migration, the retired tables should not exist:

- orders
- order_status_history
- order_documents
- payments

The following current business/CMS tables should remain, including `quote_requests` for the lightweight Leads workflow.
