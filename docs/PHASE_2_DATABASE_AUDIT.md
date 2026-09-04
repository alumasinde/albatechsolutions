# Phase 2 Database Audit

## Active model

Service Category -> Service -> Assistance Request -> Quote -> Payment Verification -> Work -> Completion

## Keep

Identity and security: users, roles, permissions, role_permissions, user_roles, user_tokens, audit_logs and rate_limits.

Site/content: settings, media, service_categories, services, service_commerce, blog categories/posts, FAQs and contact messages.

Assistance: requests, history, quotes, payments, work, updates, reviews and notifications.

## Retired

Do not build new features on legacy orders/payments, customer self-service assumptions, projects, growth analytics, digital assistant tables, generic public CMS pages as primary service landing pages, or fake sample content.

Historical migrations remain for deployed installation safety.
