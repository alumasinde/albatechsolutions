-- 018: Tighten service metadata to search-result length, remove unverifiable claims
-- and give the three thinnest service pages practical, useful detail.
-- Updates that target seeded copy are conditional so administrator edits are preserved.

UPDATE services SET
  meta_description = 'Independent help with SHA and SHIF registration in Kenya, including linking dependents. Confirm the required documents before you start. Enquire on WhatsApp.'
WHERE slug = 'sha-registration-help-in-kenya'
  AND meta_description = '• Transitioning from NHIF to SHA? AlbaTech Solutions provides independent, practical help to complete your SHA registration and link family dependents without portal errors. Get step-by-step guidance over WhatsApp today!';

UPDATE services SET
  meta_description = 'Mobile-friendly business websites for Kenyan SMEs, with clear layouts, WhatsApp enquiries and basic SEO. Agree the scope and costs before work starts.'
WHERE slug = 'website-design'
  AND meta_description = 'Looking for a practical, affordable website for your business in Kenya? AlbaTech Solutions designs fast, mobile-friendly websites built to build trust and attract local customers. Get clear layouts, seamless WhatsApp integration, and basic Google SEO. Contact us today to get started!';

UPDATE services SET
  meta_description = 'Independent help with KRA returns filing in Kenya. Organise your documents, understand the iTax steps and confirm what is due before you file.'
WHERE slug = 'kra-returns-filing'
  AND meta_description = 'Need help filing your KRA returns in Kenya? AlbaTech Solutions provides practical, independent assistance for individuals and businesses. We help you organize your documentation, avoid penalties, and easily navigate the iTax portal. Get clear, step-by-step guidance over WhatsApp today.';

UPDATE services SET
  meta_description = 'Independent help with NTSA Smart DL applications, licence renewals and vehicle ownership transfers in Kenya. Confirm what is required before you start.'
WHERE slug = 'ntsa-smart-dl-assistance'
  AND meta_description = '• Strugling with the NTSA portal? AlbaTech Solutions provides independent, practical assistance for Smart DL applications, driving license renewals, and vehicle ownership transfers in Kenya. Skip the confusion, chat with us on WhatsApp today!';

UPDATE services SET
  meta_title = 'Landing Page Design in Kenya | AlbaTech Solutions',
  meta_description = 'Focused landing pages for Kenyan ad campaigns, built to load quickly on mobile and make the next step obvious. Request a quote with your campaign details.',
  short_description = 'Get a focused, single-page website built around one product, service or campaign. A good landing page removes distractions and points visitors to one action: calling, sending an enquiry or starting a WhatsApp chat. Fast, mobile-responsive and clear about what happens next.'
WHERE slug = 'landing-page-design'
  AND meta_title = 'High-Converting Landing Page Design Kenya | AlbaTech Solutions';

UPDATE services SET
  meta_title = 'Business Registration Help in Kenya | AlbaTech Solutions',
  meta_description = 'Independent help preparing business registration documents for eCitizen in Kenya, including name search, company structure and submission steps.'
WHERE slug = 'business-registration'
  AND meta_title = 'Business Registration Assistance in Kenya | AlbaTech Solutions';

UPDATE services SET
  meta_title = 'Google Business Profile Setup Kenya | AlbaTech Solutions',
  meta_description = 'Set up and verify a Google Business Profile for your Kenyan business. Add accurate hours, location, services and contact details so customers can find you.',
  short_description = 'Help local customers find your business on Google Search and Maps with a complete, accurate profile. We help you set up and structure your business details so your location, hours, services and contact options are easier to find and use.',
  who_for = REPLACE(who_for, '• New businesses eager to build instant social proof through local customer reviews.', '• New businesses that want a complete profile in place before they start asking customers for reviews.'),
  description = REPLACE(description, 'work flawlessly', 'work correctly')
WHERE slug = 'google-business-profile-setup'
  AND meta_title = 'Google Business Profile & Maps Setup Kenya | AlbaTech Solutions';

UPDATE services SET
  meta_title = 'eTIMS Registration & Invoice Support | AlbaTech Solutions',
  meta_description = 'Independent help understanding KRA eTIMS registration and electronic invoice setup. Confirm what applies to your business before you start.',
  description = 'eTIMS is KRA''s electronic tax invoicing system. Businesses that need to issue tax invoices are expected to generate them through a system that transmits invoice data to KRA. Requirements depend on your tax profile, how you sell and whether you supply government agencies.

We start with an eligibility review, then guide you through eTIMS registration on iTax, invoice workflow setup and a check of the records you already issue. If you sell to government agencies, we help you confirm the invoicing information those buyers expect. Bring your KRA PIN, business details and a sample of the sales or invoice records you use.

Fees, penalties and compliance decisions remain with KRA. AlbaTech Solutions is an independent provider, not a KRA agent, and we never ask for your iTax password or one-time verification codes.'
WHERE slug = 'etims-invoice-support'
  AND meta_title = 'eTIMS Registration & Invoice Support in Kenya | AlbaTech Solutions';

UPDATE services SET
  meta_description = 'Confirm requirements and official fees before applying for an eCitizen service in Kenya, with independent step-by-step guidance.',
  description = 'eCitizen is the portal used to apply for many Kenyan government services, including civil registration documents, passport-related steps, HELB applications and business permits. Each service has its own requirements, fees and appointment rules, and those details can change.

We help you identify the correct official service before you pay, confirm the documents and fee that apply, prepare the application information and work through appointment booking where the process requires it. The aim is to reduce avoidable mistakes such as choosing the wrong service, uploading the wrong document or missing a required detail.

You will normally need your National ID, KRA PIN and the supporting documents specific to the service, for example a birth notification, passport details or academic records. Fees are paid to the relevant government agency, and decisions and processing times remain with that agency. AlbaTech Solutions is an independent provider, not a government office, and we never ask for your portal password, PIN or one-time verification codes.'
WHERE slug = 'ecitizen-application-assistance'
  AND meta_description = 'Independent help with eCitizen services in Kenya: birth certificates, exit permits, HELB applications and more. Correct requirements and fees, confirmed first.';

UPDATE services SET
  meta_title = 'BRS Annual Returns Filing in Kenya | AlbaTech Solutions',
  meta_description = 'Independent help with BRS annual returns for Kenyan companies, including late filings and the information you need before you submit.',
  description = 'Every company registered in Kenya is expected to file annual returns with the Business Registration Service. The return records details such as the company''s directors, shareholders and shareholding for the relevant year. It is separate from your tax returns, and a company can fall out of good standing even if it has been inactive or made no sales.

We start by reviewing your current filing status and the latest CR12, then prepare a checklist of the information needed for the return. We guide you through the BRS filing process, help you keep the supporting records and flag anything that needs correcting before submission. If returns are already overdue, we help you understand the late-filing position and the steps to get back into good standing.

Bring the company name or registration number, the latest CR12 and current director and shareholder details. Fees and penalties are set by the Registrar. AlbaTech Solutions is an independent provider and not the Business Registration Service.'
WHERE slug = 'annual-returns-brs-compliance'
  AND meta_title = 'Company Annual Returns Filing in Kenya (BRS) | AlbaTech Solutions';

UPDATE site_settings SET setting_value = 'Website Design & Business Registration in Kenya | AlbaTech'
WHERE setting_key = 'home_meta_title'
  AND setting_value = 'Website Design, Business Registration & Digital Services in Nairobi | AlbaTech Solutions';

UPDATE site_settings SET setting_value = 'Practical website design, business registration and KRA filing help for Kenyan businesses. Clear scope agreed before work starts. Enquire on WhatsApp.'
WHERE setting_key = 'home_meta_description'
  AND setting_value = 'Affordable website design, business registration, KRA returns filing and practical digital services for businesses in Nairobi and across Kenya. Clear pricing and fast WhatsApp support.';

UPDATE site_settings SET setting_value = 'Practical website design, business registration and KRA filing help for individuals and small businesses in Kenya.'
WHERE setting_key = 'default_meta_description'
  AND setting_value = 'Practical web design and digital assistance for individuals and businesses in Kenya.';
