# Phase 2 Deep Dependency Trace

## Final decisions

| Area | Decision | Reason |
|---|---|---|
| service_commerce | KEEP + MERGE LATER | Active public/admin catalogue dependency; useful operational metadata, but too broad as a separate "commerce" concept |
| Customer remnants | RETIRE | Public workflow now uses assistance requests and secure token links, not customer accounts |
| Projects | RETIRE | No active public routes; portfolio claims should not be maintained as a separate module until real case studies exist |
| Growth analytics | RETIRE FROM RUNTIME | Public collection/dashboard were already removed; repository still couples analytics to retired pages, assistant and commerce |
| Digital assistant | RETIRE | No active route; repository and permissions remain only as dead dependencies |

## Evidence

### service_commerce
Active dependency chain:

    ServiceController -> ServiceCatalogueService -> ServiceRepository -> service_commerce

Public service queries also join service_commerce. This means dropping it now would break the catalogue.

Keep the table for the current release, but treat only these concepts as active:
- pricing mode
- customer fee guidance
- government fee note/disclaimer where applicable
- turnaround guidance
- requirements
- intake questions
- quote/instant-request flags

Related-service IDs and internal notes should not drive public rendering.

### Customer remnants
CustomerMiddleware has no active route usage. The public workflow is:

    visitor -> get-help -> assistance request -> secure token links

Legacy PaymentController still references Orders and customer IDs, but no active routes use that controller. This is dead legacy code, not the current payment workflow.

### Projects
Project repository/controller/views remain, but public/admin routes were removed. JavaScript analytics still contains a stale /projects branch.

### Growth analytics
GrowthAnalyticsRepository still queries:
- growth_page_views
- growth_events
- growth_content_notes
- pages
- service_commerce
- assistant tables

That makes it a cross-cutting dependency on systems already retired from the public product. Runtime collection/dashboard are not active.

### Digital assistant
AssistantRepository remains, but no route uses it. Growth analytics is the only remaining runtime-style dependency discovered.

## Phase 2 baseline after cleanup

KEEP:

    settings/media
    service_categories/services/service_commerce
    blog/faqs/contact messages
    assistance request -> quote -> payment -> work -> completion
    admin identity/security/audit

RETIRE FROM APPLICATION RUNTIME:

    CustomerMiddleware
    legacy order payment controller
    project repository/controller/views
    growth analytics repository
    assistant repository
    stale project analytics branches

Historical migrations remain append-only for deployment safety.
