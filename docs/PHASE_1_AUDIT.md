# Phase 1 Architecture Audit

## Decision
AlbaTech is a service business website with an internal assistance workflow. The repository had grown into overlapping products: CMS, public site, customer account/order checkout, assistance workflow, digital assistant, growth analytics and a portfolio subsystem.

Phase 1 keeps the useful core and removes the retired parallel paths from the active application.

## Keep
- Core framework, configuration, database, validation, CSRF, rate limiting and security headers
- Admin authentication, roles, permissions, audit logging and settings
- CMS content needed for Services, Guides, FAQs, media and contact messages
- Service catalogue
- Assistance requests, quotes, payment verification, work tracking and notifications
- Blog/guides and SEO routes

## Retire from the active application
- Public registration and Google customer authentication
- Customer account portal
- Legacy Orders module and legacy order-payment checkout
- Public/administrative digital assistant routes
- Growth analytics collection and intelligence dashboard
- Public projects/portfolio routes and project management
- Generic public Pages CMS routing

## Public information architecture
Home -> Services -> Service pages -> Guides -> About -> Contact/Get Help

The primary conversion path is WhatsApp and the Get Help form. Internal terminology such as orders, leads or AI sessions is not exposed as public navigation.

## Database safety
Historical migrations are retained. Existing deployments may have applied them. Runtime routes and modules are removed first; Phase 2 can consolidate a fresh-install baseline after data migration planning.

## Follow-up
Phase 2 introduces clean AlbaTech seed data for service categories/services and replaces sample content with production-ready content.
