-- Expand the Government & Business Assistance catalogue with three high-intent Kenyan services.
-- Content reflects the official position as at October 2026 (KRA eTIMS enforcement, BRS fee schedule).
-- AlbaTech is an independent assistance provider; all applications and decisions stay with the agencies.

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'eTIMS Registration & Invoice Support', 'etims-invoice-support',
       'Get help understanding KRA eTIMS registration and issuing compliant electronic tax invoices for your business.',
       'From 1 January 2026 KRA increasingly matches declared income and expenses against eTIMS invoice data, and government suppliers must submit eTIMS-backed invoices through IFMIS. We provide independent, practical assistance with eTIMS registration, invoice workflow setup and compliance checklists so your records match what you file. Requirements depend on your tax profile; we confirm the scope and the current official position before starting.',
       'VAT-registered and non-VAT businesses that need to issue eTIMS electronic tax invoices, including suppliers working with government agencies.',
       'Eligibility review, guidance through eTIMS registration on iTax, invoice workflow setup assistance, device or software option explanation and a compliance checklist within the agreed scope.',
       'Your KRA PIN and tax profile details, business information and the sales or invoice records you need to issue. Never share your iTax password with anyone.',
       'quote', 'Ask about pricing', 'eTIMS Registration & Invoice Support in Kenya | AlbaTech Solutions',
       'Practical help with KRA eTIMS registration and electronic tax invoicing for Kenyan businesses, reflecting the 2026 compliance changes.',
       'published', 1, 30
FROM service_categories WHERE slug = 'government-business-assistance';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'eCitizen Application Assistance', 'ecitizen-application-assistance',
       'Clear, step-by-step help with common eCitizen government applications, without the guesswork.',
       'We guide you through popular eCitizen services such as birth and death certificate applications, exit permit requests, HELB-related applications and other documented processes. Requirements, fees and timelines are set by each agency and can change, so we confirm the current official position before you apply and help you prepare a complete, accurate submission.',
       'Individuals and businesses applying for government services through eCitizen who want the requirements confirmed before they start.',
       'Identification of the correct official service, requirements and fee checklist, application form preparation guidance and appointment scheduling help within the agreed scope.',
       'Your National ID, KRA PIN and the supporting documents the specific service requires, for example a birth notification or passport details.',
       'quote', 'Ask about pricing', 'eCitizen Application Assistance in Kenya | AlbaTech Solutions',
       'Independent help with eCitizen services in Kenya: birth certificates, exit permits, HELB applications and more. Correct requirements and fees, confirmed first.',
       'published', 0, 40
FROM service_categories WHERE slug = 'government-business-assistance';

INSERT IGNORE INTO services
(category_id, name, slug, short_description, description, who_for, what_included, what_needed, pricing_type, pricing_label, meta_title, meta_description, status, is_featured, sort_order)
SELECT id, 'Company Annual Returns & BRS Compliance', 'annual-returns-brs-compliance',
       'Understand the annual returns your company must file with the Companies Registry and prepare them correctly.',
       'Kenyan companies must file annual returns with the Registrar of Companies through the Business Registration Service portal within the statutory window. Late or missing filings attract penalties and, if left unaddressed, can put a company at risk of being struck off the register. We help directors and company secretaries review filing status, prepare the required company information and understand the official process. Filing requirements and penalties follow the current Companies Act and BRS fee schedule.',
       'Directors and company secretaries of private limited companies that need to file annual returns or get back into good standing.',
       'Filing status review, a checklist of required information such as shareholding and director details, guidance through the BRS filing process and record-keeping advice.',
       'Company name or registration number, the latest CR12, current director and shareholder details and the relevant financial year information.',
       'quote', 'Ask about pricing', 'Company Annual Returns Filing in Kenya (BRS) | AlbaTech Solutions',
       'Help with BRS annual returns, late filings and Companies Registry compliance in Kenya. Know what is due before penalties build up.',
       'published', 0, 50
FROM service_categories WHERE slug = 'government-business-assistance';
