# MASTER DEVELOPMENT PROMPT: ALBATECH SOLUTIONS

## 1. ROLE AND EXPECTATIONS

Act as a Senior Software Architect, Senior SvelteKit Developer, UI/UX Designer, Database Architect and Application Security Engineer.

Your task is to design and build a production-ready, enterprise-quality website and business management platform for **AlbaTech Solutions**.

Official domain: `https://albatechsolutions.co.ke`

This is not just a static company website. It is a dynamic technology company website with an integrated administration system for managing business operations.

You must think like an experienced software architect. Prioritize:

- Clean architecture.
- Modular and reusable components.
- Database-driven functionality.
- Separation of concerns.
- Maintainability.
- Scalability.
- Security.
- Performance.
- Responsive design.
- Consistent coding standards.
- Minimal code duplication.

Do not rush into generating components before establishing the architecture, database schema and application structure.

---

# 2. PROJECT OVERVIEW

AlbaTech Solutions is a technology company providing digital services to businesses and individuals across Kenya.

The company offers:

### A. Web Design and Development
- Corporate websites.
- Business websites.
- E-commerce websites.
- Portfolio websites.
- Landing pages.
- Website redesigns.
- Website maintenance.

### B. Software Development
- Custom business applications.
- Inventory management systems.
- School management systems.
- Hospital management systems.
- ERP solutions.
- CRM solutions.
- POS systems.
- API development and integrations.
- Business automation.

### C. Digital Assistance Services
Provide assistance to individuals and businesses accessing digital government and other online services in Kenya.

Examples:
- KRA services and tax-return assistance.
- eCitizen assistance.
- NTSA services assistance.
- Business registration assistance.
- Online application assistance.
- Other configurable digital services.

Important: AlbaTech provides assistance and guidance. Do not represent the company as a government agency, claim official government affiliation, or imply that it can bypass government procedures. Customers must authorize actions involving their personal information.


The service catalogue must be completely dynamic. Administrators must be able to add, edit, disable, categorize and remove services without changing source code.

---

# 3. TECHNOLOGY STACK

Use the following technologies.

| Layer | Technology |
|---|---|
| Frontend | Svelte 5 |
| Application framework | SvelteKit |
| Programming language | TypeScript |
| Database | MySQL 8.x |
| ORM | Drizzle ORM |
| Styling | Tailwind CSS |
| Validation | Zod |
| Icons | Lucide |
| Email | Nodemailer / SMTP |
| Authentication | Secure server-side sessions |
| Testing | Vitest and Playwright |
| Deployment | Node.js adapter with Nginx |

Use modern Svelte 5 conventions.

Use:
- `$state`
- `$derived`
- `$effect` only where necessary
- `$props`
- Svelte 5 event syntax.
- SvelteKit server-side loading.
- SvelteKit form actions.
- TypeScript strict mode.

Do not use deprecated Svelte patterns.

Do not introduce React, Vue, PHP, Laravel or another backend framework.

The entire application must use TypeScript and SvelteKit for frontend and backend functionality.

MySQL must be the primary persistent database.

---

# 4. CRITICAL ARCHITECTURAL REQUIREMENT: NO HARDCODED BUSINESS LOGIC

This is one of the most important requirements.

Build a dynamic, database-driven application.

Do not hardcode business data, service configurations, prices, website content, operational statuses, business settings or workflow rules inside Svelte components or route files.

Business configuration must be managed through the database and administration dashboard.

## 4.1 What must be dynamic?

The following must be database-driven:

1. Company name and branding information.
2. Logo and favicon references.
3. Contact email.
4. Telephone numbers.
5. WhatsApp number.
6. Business address.
7. Social media links.
8. Website navigation.
9. Footer content.
10. Homepage sections.
11. Homepage banners.
12. Services and service categories.
13. Service descriptions.
14. Service pricing.
15. Service availability.
16. Service requirements.
17. Service forms and their fields.
18. Portfolio projects.
19. Project categories.
20. Project technologies.
21. Testimonials.
22. Blog articles.
23. Frequently asked questions.
24. SEO metadata.
25. Website announcements.
26. Quotations and quotation templates.
27. Inquiry categories.
28. Request statuses.
29. Request workflow configurations.
30. Email notification templates.
31. Business operating settings.
32. Supported payment methods.
33. Tax and fee configurations.
34. Administrative permissions.

An administrator must be able to modify these through the admin dashboard without editing application source code.

## 4.2 Configuration-driven architecture

Create reusable configuration services.

Examples:

- SettingsService
- NavigationService
- ServiceCatalogueService
- PricingService
- WorkflowService
- NotificationTemplateService
- EmailTemplateService
- PermissionService
- FeatureFlagService

Do not scatter business configuration throughout the application.

## 4.3 Business rules versus technical invariants

Keep technical security invariants in application code.

Examples:
- Password hashing.
- Authentication verification.
- Authorization enforcement.
- SQL parameterization.
- CSRF protection.
- Transaction integrity.

Business policies should be configurable wherever practical.

For example:

- Service prices must come from the database.
- Service requirements must come from the database.
- Request statuses must be configurable.
- Workflow transitions must be configurable.
- Notification templates must come from the database.
- Tax rates must come from configuration.
- Navigation items must come from the database.
- Admin permissions must be managed through RBAC.

Do not create a generic dynamic scripting engine that allows arbitrary code execution. Use validated, typed configuration with explicit allowed actions.

## 4.4 No fake dynamic functionality

Do not create interfaces that appear dynamic but contain hardcoded arrays behind them.

For example, do not implement:

```typescript
const services = [
  { name: 'Web Development', price: 15000 },
  { name: 'Software Development', price: 50000 }
];
```

Instead, retrieve services from MySQL through a repository and service layer.

Demo content may be inserted through database seeders, but it must never be embedded as production business logic.

---

# 5. BRAND IDENTITY AND DESIGN SYSTEM

Use the supplied official AlbaTech Solutions logo as the primary brand reference.

Do not redesign or replace the logo.

The logo uses a geometric A symbol with teal branding, a charcoal wordmark and gold accent.

## 5.1 Brand colours

Use these initial colour values, subject to verification against the original logo artwork:

| Colour | Hex | Usage |
|---|---|---|
| Primary Teal | #078F9B | Buttons, links, highlights |
| Accent Gold | #FFBB38 | Highlights, selected elements |
| Charcoal | #192326 | Headings, footer |
| Light Background | #F3F6F6 | Section backgrounds |
| White | #FFFFFF | Main backgrounds |
| Muted Grey | #64748B | Secondary text |

Create centralized CSS design tokens.

Example:

```css
:root {
    --color-primary: #078F9B;
    --color-primary-hover: #067781;
    --color-accent: #FFBB38;
    --color-dark: #192326;
    --color-background: #F3F6F6;
    --color-surface: #FFFFFF;
    --color-muted: #64748B;
}
```

Avoid scattering raw colour values throughout components.

## 5.2 Visual direction

The design should communicate:

- Professionalism.
- Technical expertise.
- Reliability.
- Innovation.
- Simplicity.
- Trust.

Use a premium modern technology-agency aesthetic.

Design characteristics:

- Clean layouts.
- Generous whitespace.
- Strong typography.
- Consistent spacing.
- Subtle animations.
- Carefully designed cards.
- Professional iconography.
- High-quality project imagery.
- Clear calls to action.
- Consistent visual hierarchy.

Avoid:
- Excessive gradients.
- Excessive rounded cards.
- Too many animations.
- Generic AI-generated website layouts.
- Unnecessary decorative elements.
- Overloaded navigation.
- Excessive use of gold.
- Inconsistent typography.

The website must look professionally designed, not like an unmodified template.

---

# 6. PUBLIC WEBSITE REQUIREMENTS

Build the following pages.

## 6.1 Homepage

The homepage must be database-driven and assembled from configurable sections.

Recommended sections:

1. Hero section.
2. Company introduction.
3. Services overview.
4. Featured projects.
5. Why choose AlbaTech.
6. How we work.
7. Digital assistance section.
8. Technologies and expertise.
9. Testimonials.
10. Frequently asked questions.
11. Contact call-to-action.
12. Footer.

Each homepage section must have configurable:
- Visibility.
- Heading.
- Description.
- Images.
- Content.
- Display order.
- CTA text.
- CTA destination.

Administrators should be able to reorder sections and disable them without modifying code.

Hero section example:

Headline:

"We Build Technology That Moves Your Business Forward."

Supporting text:

"From professional websites and custom software to reliable IT solutions and digital assistance, we help businesses and individuals get more done with technology."

Primary CTA: Start a Project.

Secondary CTA: Explore Services.

Do not permanently hardcode these texts. Store them in the content management system.

## 6.2 About page

Include:
- Company introduction.
- Mission.
- Vision.
- Core values.
- Company capabilities.
- Working approach.
- Contact CTA.

All content must be editable through the administration dashboard.

## 6.3 Services

Build:
- Services listing.
- Service categories.
- Individual service details.
- Service pricing.
- Service requirements.
- Service inquiry CTA.

Each service must have:
- Name.
- Slug.
- Category.
- Description.
- Short description.
- Featured image.
- Pricing model.
- Price, where applicable.
- Availability.
- Requirements.
- Estimated turnaround time.
- SEO metadata.
- Display order.
- Publication status.

Pricing models should support:
- Fixed price.
- Starting from.
- Custom quotation.
- Free consultation.

Do not hardcode service pricing.

## 6.4 Portfolio

Build a dynamic portfolio system.

Features:
- Project listing.
- Project category filters.
- Featured projects.
- Project detail pages.
- Project image galleries.
- Technologies used.
- Project descriptions.
- Client testimonials.
- External project links.

Each project must be manageable from the admin dashboard.

Use SEO-friendly URLs:

`/projects/project-slug`

## 6.5 Pricing

Build a configurable pricing page.

Support:
- Service packages.
- Package features.
- Fixed pricing.
- Custom quotation.
- Featured packages.
- Optional add-ons.

All prices and packages must come from MySQL.

## 6.6 Contact

Include:
- Contact form.
- Configurable email address.
- Configurable telephone number.
- WhatsApp contact.
- Social media links.
- Business location, if configured.
- Map integration, if configured.

Store contact form submissions in MySQL.

Notify administrators through email.

## 6.7 Request a Quote

Build a structured quotation request form.

Fields should be configurable where practical.

Default fields:
- Full name.
- Email.
- Phone.
- Company name.
- Service category.
- Project title.
- Budget range.
- Project description.
- Attachments.

Generate a unique inquiry reference.

Store submissions in MySQL.

Send an email confirmation to the customer and an internal notification to the configured business email.

## 6.8 Digital Assistance

Create a dedicated section for services such as:
- KRA assistance.
- eCitizen assistance.
- NTSA assistance.
- Online applications.
- Business registration assistance.
- Other digital services.

Each service must have its own configurable description, requirements, pricing and request form.

Support dynamic forms based on service requirements.

Do not collect government passwords, OTPs or unnecessary sensitive credentials.

Include consent and authorization requirements where applicable.

Clearly communicate that AlbaTech is an independent assistance provider, not a government institution.

## 6.9 Blog

Build a complete content management system.

Features:
- Categories.
- Tags.
- Articles.
- Featured images.
- Author information.
- Publication scheduling.
- Drafts.
- SEO metadata.
- Related articles.

---

# 7. CUSTOMER INTERACTION MODEL

IMPORTANT: DO NOT BUILD A CUSTOMER PORTAL IN VERSION 1.

Customers should not be required to register or log in.

The initial customer interaction channels are:

1. WhatsApp.
2. Email.
3. Website contact forms.
4. Quotation request forms.
5. Digital service request forms.
6. Telephone, where configured.

Implement configurable WhatsApp contact buttons.

WhatsApp numbers must be retrieved from business settings.

Use prefilled messages containing the selected service where appropriate.

For structured inquiries, save submissions in MySQL and generate reference numbers.

Customers should receive email acknowledgements when an email address is provided.

Do not require customer accounts.

Design the backend so that a customer portal can be added in a future release without redesigning the database.

Keep customer profiles separate from administrative user accounts.

---

# 8. ADMINISTRATION DASHBOARD

Build a comprehensive, secure administration dashboard.

URL:

`/admin`

The admin dashboard is the control centre for the entire business.

## 8.1 Dashboard overview

Display configurable business statistics:

- Total inquiries.
- New inquiries.
- Active projects.
- Completed projects.
- Pending digital requests.
- Total customers.
- Pending quotations.
- Outstanding invoices.
- Revenue summaries.

Use real MySQL data.

Do not use fake dashboard statistics.

Support date filters where applicable.

## 8.2 Service management

Administrators should be able to:

- Create services.
- Edit services.
- Delete or archive services.
- Manage categories.
- Set pricing.
- Configure requirements.
- Configure service-specific forms.
- Enable or disable services.
- Manage featured services.
- Control display order.

## 8.3 Portfolio management

Allow administrators to:

- Add projects.
- Edit projects.
- Delete or archive projects.
- Upload project images.
- Assign categories.
- Add technologies.
- Mark projects as featured.
- Publish or unpublish projects.
- Manage project SEO.

## 8.4 Inquiry management

Provide a centralized inquiry management module.

Features:
- Inquiry listing.
- Search.
- Filters.
- Pagination.
- Status management.
- Assignment to staff.
- Internal notes.
- Customer details.
- Inquiry history.
- Email communication records.
- Reference number search.

All inquiry statuses and permitted transitions should be managed through configurable workflow definitions.

## 8.5 Digital request management

Administrators and authorized staff should be able to:

- View requests.
- Assign requests.
- Review customer information.
- Review attachments.
- Update request status.
- Add internal notes.
- Record customer communication.
- Record quoted amounts.
- Track completion.
- View request history.

Support configurable service workflows.

## 8.6 Customer management

Build an internal customer directory.

Features:
- Customer profiles.
- Contact information.
- Business information.
- Inquiry history.
- Quotations.
- Invoices.
- Digital service requests.
- Notes.
- Customer activity history.

Do not require customers to have login accounts.

## 8.7 Quotations

Build a quotation management module.

Features:
- Create quotations.
- Add quotation items.
- Set quantities.
- Set unit prices.
- Configure discounts.
- Apply configurable taxes.
- Calculate totals.
- Generate PDF quotations.
- Set expiry dates.
- Track quotation statuses.
- Email quotations to customers.

Quotation numbering must be generated safely and uniquely.

Preserve historical price snapshots.

## 8.8 Invoices and payments

Build internal financial management.

Features:
- Invoice creation.
- Invoice items.
- Configurable tax.
- Discounts.
- Invoice numbering.
- PDF generation.
- Payment recording.
- Payment methods.
- Payment history.
- Outstanding balances.
- Financial reports.

Prepare the architecture for future M-Pesa Daraja integration.

Do not falsely mark a payment as successful based on client-side input.

## 8.9 Content management

Administrators must be able to manage:

- Homepage content.
- Homepage sections.
- About page.
- Navigation.
- Footer.
- Services.
- Portfolio.
- Testimonials.
- Blog.
- FAQs.
- Contact information.
- SEO settings.
- Announcements.
- Media library.

The public website should retrieve content dynamically.

## 8.10 Business settings

Build a centralized settings module.

Include:
- Company name.
- Company logo.
- Favicon.
- Business email.
- Telephone.
- WhatsApp.
- Address.
- Social links.
- Currency.
- Tax configuration.
- Email configuration.
- Business hours.
- SEO defaults.
- Notification preferences.
- Feature flags.

Avoid creating separate hardcoded contact details in different components.

---

# 9. DATABASE ARCHITECTURE

Use MySQL with InnoDB and utf8mb4.

Use Drizzle ORM with versioned migrations.

Design a normalized, relational database.

Suggested modules and tables:

## Identity and authorization
- users
- roles
- permissions
- user_roles
- role_permissions
- sessions
- password_resets

## Website content
- pages
- page_sections
- navigation_menus
- navigation_items
- services
- service_categories
- service_requirements
- service_form_fields
- projects
- project_categories
- project_images
- technologies
- project_technologies
- testimonials
- posts
- post_categories
- post_tags
- faqs
- media

## CRM
- customers
- inquiries
- inquiry_notes
- inquiry_assignments
- inquiry_activities
- quotations
- quotation_items

## Digital services
- digital_requests
- digital_request_files
- digital_request_updates
- workflow_definitions
- workflow_statuses
- workflow_transitions

## Finance
- invoices
- invoice_items
- payments
- payment_transactions
- payment_methods
- tax_configurations

## System
- settings
- notification_templates
- email_templates
- notifications
- audit_logs
- feature_flags

Review and adjust the schema based on actual entity relationships.

Do not create unnecessary tables simply to increase the table count.

Use appropriate foreign keys, indexes and unique constraints.

Use BIGINT UNSIGNED primary keys where appropriate.

Use DECIMAL(12,2) for monetary values.

Use explicit currency codes.

Use UTC timestamps.

Do not use floating-point columns for money.

Avoid storing relational data in JSON when a proper relational design is more appropriate.

JSON is acceptable for validated configurable form schemas and flexible metadata.

Use transactions for operations involving multiple related database changes.

## Important data separation

Distinguish between:

- Administrative users.
- Customers.
- Public website visitors.
- Inquiries.
- Operational projects.
- Portfolio projects.
- Digital service requests.

Portfolio projects must not be confused with operational customer projects.

Customer records must not require administrative user accounts.

---

# 10. APPLICATION ARCHITECTURE

Use a modular monolithic architecture.

Avoid building a monolithic collection of unrelated route files.

Recommended structure:

```text
src/
├── app.html
├── app.css
│
├── routes/
│   ├── (marketing)/
│   │   ├── +layout.svelte
│   │   ├── +page.svelte
│   │   ├── about/
│   │   ├── services/
│   │   ├── projects/
│   │   ├── pricing/
│   │   ├── blog/
│   │   ├── contact/
│   │   ├── request-quote/
│   │   └── request-service/
│   │
│   ├── admin/
│   │   ├── login/
│   │   ├── +layout.server.ts
│   │   ├── +layout.svelte
│   │   ├── +page.svelte
│   │   ├── services/
│   │   ├── projects/
│   │   ├── inquiries/
│   │   ├── customers/
│   │   ├── quotations/
│   │   ├── digital-requests/
│   │   ├── invoices/
│   │   ├── payments/
│   │   ├── content/
│   │   ├── media/
│   │   ├── users/
│   │   ├── reports/
│   │   └── settings/
│   │
│   └── api/
│       └── v1/
│
├── lib/
│   ├── components/
│   │   ├── ui/
│   │   ├── marketing/
│   │   ├── admin/
│   │   └── forms/
│   │
│   ├── server/
│   │   ├── db/
│   │   │   ├── index.ts
│   │   │   └── schema/
│   │   │
│   │   ├── auth/
│   │   ├── repositories/
│   │   ├── services/
│   │   ├── validators/
│   │   ├── permissions/
│   │   ├── workflows/
│   │   ├── email/
│   │   ├── storage/
│   │   ├── payments/
│   │   └── security/
│   │
│   ├── utils/
│   ├── types/
│   └── constants/
│
├── hooks.server.ts
└── app.d.ts

drizzle/
├── migrations/
└── meta/

static/
├── images/
├── icons/
└── favicon.png

tests/
├── unit/
├── integration/
└── e2e/

scripts/
├── create-admin.ts
└── seed.ts
```

Adjust the structure when necessary, but preserve separation of concerns.

## Architecture rules

Repositories:
- Responsible for database access.
- No UI logic.
- No HTTP request handling.

Services:
- Responsible for business operations.
- Coordinate repositories.
- Enforce business rules.
- Handle database transactions.

Validators:
- Validate incoming data.
- Define reusable Zod schemas.

Routes:
- Handle requests.
- Validate input.
- Enforce authorization.
- Call services.
- Return appropriate responses.

Components:
- Present UI.
- Receive typed properties.
- Avoid direct database access.
- Avoid duplicating business logic.

Never access MySQL directly from client-side components.

Never expose database credentials or server-only modules to the browser.

---

# 11. AUTHENTICATION AND SECURITY

Implement administrator authentication only for the initial release.

Use:
- Secure password hashing with Argon2id or properly configured bcrypt.
- Server-managed opaque sessions.
- HttpOnly cookies.
- Secure cookies in production.
- SameSite cookie policy.
- Session rotation.
- Session expiration.
- CSRF protection.
- Rate limiting.
- Password reset with expiring tokens.
- RBAC.
- Audit logging.

Implement authorization checks on every protected server operation.

Do not rely on hiding admin navigation links as a security mechanism.

Do not trust client-supplied:
- User IDs.
- Prices.
- Roles.
- Permissions.
- Payment statuses.
- Request ownership.
- Workflow transitions.

Validate all input on the server.

Prevent:
- SQL injection.
- XSS.
- CSRF.
- IDOR.
- Broken access control.
- Session fixation.
- Unauthorized file access.
- Mass assignment.
- Unsafe file uploads.

Use private storage for customer documents.

Never store plaintext passwords or sensitive authentication credentials.

Never collect customers' government portal passwords or OTPs.

Implement audit logs for sensitive administrative operations.

---

# 12. REUSABLE COMPONENTS

Build a reusable component library.

Examples:

UI:
- Button
- Input
- Textarea
- Select
- Checkbox
- Modal
- Dialog
- Dropdown
- Badge
- Card
- DataTable
- Pagination
- SearchInput
- DatePicker
- FileUpload
- Toast
- ConfirmationDialog
- LoadingSpinner
- EmptyState

Marketing:
- Header
- Footer
- HeroSection
- ServiceCard
- ProjectCard
- TestimonialCard
- SectionHeading
- CTASection
- WhatsAppButton
- ContactInformation

Admin:
- AdminSidebar
- AdminHeader
- DashboardStatCard
- DataTable
- FilterPanel
- FormDialog
- StatusBadge
- ActivityTimeline
- PageHeader
- Breadcrumbs

Avoid duplicated markup and styling.

Use consistent form components and validation patterns.

---

# 13. EMAIL AND NOTIFICATION SYSTEM

Implement a centralized email service.

Support configurable SMTP settings through secure environment configuration.

Build reusable email templates stored in the database.

Email events should include:
- New inquiry.
- Quote request received.
- Customer inquiry acknowledgement.
- Quotation issued.
- Invoice issued.
- Payment confirmation.
- Digital request received.
- Digital request status updated.

Allow administrators to manage email template content where appropriate.

Do not hardcode email content in every route.

Use a notification queue or a reliable background-job approach for operations that should not delay the user response.

Never expose SMTP credentials in the frontend.

---

# 14. SEO AND PERFORMANCE

Implement proper SEO.

Requirements:
- Server-side rendering for public pages.
- Dynamic page metadata.
- Dynamic SEO titles and descriptions.
- Canonical URLs.
- Open Graph metadata.
- XML sitemap.
- robots.txt.
- Structured data where appropriate.
- Clean URLs.
- Optimized images.
- Lazy loading.
- Internal linking.
- Accessible semantic HTML.

Use database-driven SEO settings.

Optimize MySQL queries and apply pagination to large datasets.

Avoid unnecessary client-side JavaScript.

Use caching carefully, especially for frequently accessed public content.

Invalidate relevant cached content when administrators update it.

---

# 15. API ARCHITECTURE

Organize API endpoints under:

`/api/v1/`

Use a versioned API structure.

Example:

```text
/api/v1/services
/api/v1/projects
/api/v1/inquiries
/api/v1/quote-requests
/api/v1/digital-requests
/api/v1/contact
/api/v1/webhooks
```

Do not duplicate business logic between API endpoints and SvelteKit form actions.

Both should call the same service layer.

Implement consistent response formats and error handling.

Do not expose administrative endpoints without authentication and authorization.

---

# 16. TESTING REQUIREMENTS

Implement tests throughout development.

Unit tests:
- Validation.
- Pricing calculations.
- Quotation calculations.
- Workflow transitions.
- Permission checks.
- Reference number generation.

Integration tests:
- Database repositories.
- Inquiry submission.
- Service creation.
- Quotation creation.
- Invoice creation.
- Payment reconciliation.

End-to-end tests:
- Homepage navigation.
- Contact form submission.
- Quote request submission.
- Admin login.
- Service management.
- Project management.
- Inquiry management.
- Unauthorized access prevention.

Test:
- Empty states.
- Invalid input.
- Duplicate submissions.
- Database failures.
- Permission failures.
- Expired sessions.
- File upload restrictions.

Do not claim functionality is complete without testing it.

---

# 17. IMPLEMENTATION PHASES

Build the application in controlled phases.

Do not attempt to generate the entire application in one massive code-generation operation.

## PHASE 1: Foundation

- Initialize SvelteKit.
- Configure Svelte 5 and TypeScript.
- Configure Tailwind.
- Set up MySQL.
- Configure Drizzle.
- Establish environment configuration.
- Create initial database migrations.
- Create the application structure.
- Establish design tokens.
- Build reusable UI components.

Deliverable: Running application foundation.

## PHASE 2: Public Website

- Header and navigation.
- Homepage.
- About page.
- Services.
- Portfolio.
- Pricing.
- Contact.
- Quote request.
- Digital assistance.
- Footer.
- Responsive layouts.

Connect every content module to MySQL.

Deliverable: Functional public website.

## PHASE 3: Admin Authentication

- Admin login.
- Secure sessions.
- Roles.
- Permissions.
- Protected routes.
- Admin layout.
- Dashboard.

Deliverable: Secure administration foundation.

## PHASE 4: CMS

- Services management.
- Categories.
- Project management.
- Homepage content management.
- Navigation management.
- Testimonials.
- FAQs.
- Blog.
- Media management.
- SEO settings.

Deliverable: Fully dynamic public website controlled by the admin.

## PHASE 5: CRM

- Customer management.
- Inquiry management.
- Inquiry assignments.
- Internal notes.
- Email notifications.
- Quotations.
- PDF generation.

Deliverable: Operational customer management system.

## PHASE 6: Digital Services

- Digital service catalogue.
- Dynamic requirements.
- Configurable forms.
- Request submission.
- Reference generation.
- Request assignment.
- Status management.
- Request history.
- File attachments.

Deliverable: Digital assistance management system.

## PHASE 7: Finance

- Invoices.
- Invoice items.
- Payment recording.
- Payment methods.
- Financial reports.
- M-Pesa integration preparation.

Deliverable: Internal financial management.

## PHASE 8: Production Readiness

- Automated tests.
- Security review.
- Performance optimization.
- SEO validation.
- Backup and restoration.
- Logging.
- Error monitoring.
- Production deployment.

Deliverable: Production-ready application.

---

# 18. DEVELOPMENT RULES

These rules are mandatory.

1. Do not generate the entire project as one giant component.
2. Do not put database queries inside Svelte components.
3. Do not duplicate business logic.
4. Do not hardcode business data.
5. Do not hardcode service prices.
6. Do not hardcode navigation links.
7. Do not hardcode contact details.
8. Do not hardcode workflow statuses when they should be configurable.
9. Do not hardcode permissions into UI components.
10. Do not use fake dashboard statistics.
11. Do not use placeholder functionality in production modules.
12. Do not create unnecessary dependencies.
13. Do not introduce unnecessary abstractions.
14. Do not expose secrets.
15. Do not skip database migrations.
16. Do not disable TypeScript strict checking.
17. Do not bypass server-side validation.
18. Do not use client-side authorization as the only access control.
19. Do not introduce a customer portal in Version 1.
20. Do not claim features are complete when they are not implemented.

Every feature must have:
- A clear responsibility.
- Appropriate validation.
- Proper error handling.
- Authorization where required.
- Database persistence where required.
- Appropriate tests.

Maintain a development document containing:
- Implemented features.
- Pending features.
- Database changes.
- Architectural decisions.
- Known issues.
- Next development tasks.

Update this document after every implementation phase.

---

# 19. EXPECTED DELIVERABLES

Build a complete working repository.

The final project should include:

1. SvelteKit application.
2. Svelte 5 components.
3. TypeScript implementation.
4. Drizzle database schemas.
5. Versioned MySQL migrations.
6. Database seed scripts.
7. Admin authentication.
8. Role and permission management.
9. Dynamic public website.
10. Admin dashboard.
11. CMS.
12. CRM.
13. Digital service request management.
14. Quotation management.
15. Invoice and payment management.
16. Email notification system.
17. Responsive design.
18. SEO implementation.
19. Automated tests.
20. `.env.example`.
21. README.md.
22. Deployment documentation.
23. Backup and recovery documentation.
24. Architecture documentation.

Do not leave core functionality as TODO comments or simulated data.

---

# 20. INITIAL EXECUTION INSTRUCTIONS

Before writing code:

1. Inspect the existing repository.
2. Identify existing technologies and project structure.
3. Identify existing code that can be reused.
4. Review the supplied AlbaTech logo and brand colours.
5. Identify missing configuration.
6. Propose the final architecture.
7. Design the database schema and relationships.
8. Present the implementation roadmap.
9. Identify technical risks.

Then begin implementation.

If the repository is empty, initialize the project.

If an existing project is present, do not destroy or overwrite working functionality without first understanding it.

Work incrementally.

At the end of each phase:

- Run the application.
- Run TypeScript checks.
- Run linting.
- Run relevant tests.
- Verify database migrations.
- Fix errors.
- Document completed work.
- Report what remains.

If a required business detail is unavailable, make it configurable rather than inventing permanent company information.

Use sensible development seed data only where necessary.

Never use seed data as a substitute for real database integration.

## FINAL ARCHITECTURAL OBJECTIVE

The finished AlbaTech Solutions platform must behave like a configurable business application.

An administrator should be able to log in and:

- Add a new service.
- Change service prices.
- Create a new service category.
- Update the homepage.
- Add portfolio projects.
- Change company contact information.
- Update WhatsApp details.
- Modify navigation.
- Publish articles.
- Manage inquiries.
- Manage digital assistance requests.
- Issue quotations.
- Create invoices.
- Manage business settings.

All these operations must work without editing application source code.

**Build AlbaTech Solutions as a modular, database-driven technology business platform using Svelte 5, SvelteKit, TypeScript, MySQL and Drizzle ORM.**