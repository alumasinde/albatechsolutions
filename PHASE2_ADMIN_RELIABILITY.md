# Phase 2 — Admin Panel Reliability

## Included
- Added explicit existence checks before editing services and categories so stale IDs cannot produce false-success feedback.
- Added category create/update audit events to the existing admin audit log.
- Tightened price input validation to reject excessive precision and malformed numeric values before saving.
- Added an automated baseline test for public visibility rules, homepage featuring, price modes, CSRF coverage, POST mutation routes, and audit/pricing schema.

## Run checks
```powershell
php tests/security_baseline.php
php tests/admin_workflows_baseline.php
```

## Required local acceptance test
The automated baseline is a source-level check; it does not replace tests against your MySQL database. After running migrations, log in and verify create/edit/publish/unpublish/feature/unfeature for services, fixed/starting/quote pricing, category activation, and settings persistence. Confirm drafts and services in inactive categories never appear publicly. Review `admin_audit_logs` after changes.

## Known scope boundary
No live MySQL integration test was possible in this build environment. This phase does not claim to be a penetration test or a full browser end-to-end test.
