# Phase 3.3 Dynamic Service Pages

The public service page is one shared renderer: resources/views/public/service-show.php.

All service-specific content is data-driven through the existing services and service_commerce records:
- name, slug and category
- summary and description
- meta title and meta description
- icon and featured status
- requirements
- intake questions
- pricing mode, fee and quote requirement
- official fee notes and disclaimers
- related service IDs

Priority service URLs are published from the same model:
- /services/kra-returns-filing
- /services/ecitizen-services
- /services/business-registration
- /services/cv-writing
- /services/website-design-kenya
- /services/computer-repair
- /services/it-support
- /services/google-business-profile-setup

Adding a new service requires publishing data; it does not require a new public PHP template.
