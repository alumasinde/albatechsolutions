# Phase 2 Consolidated Architecture

## Canonical public flow

Visitor -> Service / Get Help -> Assistance Request -> Quote -> Payment Verification -> Work -> Completion -> Review

## Keep
- Identity, roles, permissions, audit logs, rate limits
- Settings and media
- Service categories and services
- Service operational metadata
- Blog, FAQs and contact messages
- Assistance workflow, quotes, verified payments, work and request notifications

## Merge / simplify
`service_commerce` is an operational extension of a service, not a separate commerce product.
Publicly meaningful fields: pricing guidance, fee notes/disclaimers, turnaround, requirements, intake questions, request flags.
Do not expose internal notes. Related services should be rendered only when explicitly useful.

## Retire from application runtime
- Customer account portal and customer-level notification preferences
- Legacy orders and order payment controller
- Projects/portfolio module
- Growth analytics runtime and admin intelligence
- Digital assistant
- Their smoke-test coverage and stale QA references

## Migration policy
Historical migrations are append-only. New cleanup migrations may remove inactive permissions and references but must not assume an empty production database.
