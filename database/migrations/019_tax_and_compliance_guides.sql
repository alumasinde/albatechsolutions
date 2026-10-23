-- 019: High-intent compliance guides and one accuracy fix on the SHA service copy.
-- Deadlines reflect KRA and BRS guidance checked on 23 October 2026, with a
-- confirmation caveat because fees and deadlines can be revised.

UPDATE services SET short_description = 'Get clear, independent assistance migrating from NHIF, creating your SHA account and updating your household dependents. Registration with the Social Health Authority is part of Kenya''s health cover transition, and dependent registration, biometric next steps and portal entry errors can be confusing. We help you organise your details and work through the process.'
WHERE slug = 'sha-registration-help-in-kenya'
  AND short_description LIKE '%mandatory for all Kenyans%';

INSERT IGNORE INTO guides
(title, slug, category, summary, intro, content_html, meta_title, meta_description, status, published_at)
VALUES (
'How to File a KRA Nil Return in Kenya (2026 Guide)',
'how-to-file-a-kra-nil-return-in-kenya',
'Tax compliance',
'A nil return tells KRA you had no taxable income for the year. Here is who should file one, the current deadline, the iTax steps and the mistakes that create unnecessary penalties.',
'A nil return is not a way to skip filing. It is the return you submit when you are registered for tax but had no income for the year. Filing on time keeps your KRA record current and avoids the default assessments that follow missed returns.',
'<p>A <strong>nil return</strong> is an income tax return that reports no taxable income for a year. If you have a KRA PIN but earned nothing during the year, you may still be required to file. Submitting a nil return is different from doing nothing: it tells KRA that you reviewed the year and had no income to declare.</p>
<h2>Who should file a nil return?</h2>
<p>KRA requires taxpayers with a PIN to file returns. A nil return can be appropriate if you had no income during the year, for example a student or dependant with a PIN, someone between jobs for the whole year, or a registered taxpayer whose business did not operate.</p>
<p>Do not file a nil return if you received any income that should be declared, including salary, business income, rental income, consultancy fees or other taxable payments. If you had income, file a normal return even when tax was already deducted.</p>
<h2>When is it due?</h2>
<p>Individual income tax returns cover a calendar year. For the 2025 year of income, the filing window ran from 1 January 2026 to 30 June 2026. Under the Finance Act 2026, individual returns are moving to a 30 April deadline from the 2026 year of income, which means the return for 2026 is due by 30 April 2027.</p>
<p>Deadlines can be changed and KRA may issue extensions or public notices. Confirm the current date on <a href="https://www.kra.go.ke" target="_blank" rel="noopener">kra.go.ke</a> before filing, especially if you are filing late.</p>
<h2>What you need</h2>
<ul>
<li>Your KRA PIN and iTax password, or the KRA M-service app if you use it.</li>
<li>The year of income you are filing for.</li>
<li>A reliable internet connection and a way to save the acknowledgement.</li>
</ul>
<h2>How to file a nil return on iTax</h2>
<ol>
<li>Sign in to iTax with your PIN and password. Do not share the password or a one-time code with anyone.</li>
<li>Open the Returns menu and choose the option to file a return.</li>
<li>Select Income Tax, then the individual return type, and choose the correct year of income.</li>
<li>Choose the nil return option and confirm that you had no income for that year.</li>
<li>Review the details and submit the return.</li>
<li>Download and keep the acknowledgement receipt. It is your evidence that the return was filed.</li>
</ol>
<p>The exact menu labels can change as iTax is updated. If the portal offers a nil return through the KRA M-service app, the same principle applies: select the correct year and keep the confirmation.</p>
<h2>Common mistakes</h2>
<ul>
<li><strong>Filing nil when you had income.</strong> This can lead to default assessments, interest and penalties when KRA matches your records.</li>
<li><strong>Doing nothing because you earned nothing.</strong> A missing return is treated differently from a filed nil return.</li>
<li><strong>Filing for the wrong year.</strong> Check the year of income before submitting.</li>
<li><strong>Not saving the acknowledgement.</strong> You may need it later for a loan, tender, visa or tax clearance.</li>
<li><strong>Sharing your iTax credentials.</strong> A legitimate provider should guide you without asking for your password or one-time codes.</li>
</ul>
<h2>What if you are late?</h2>
<p>File as soon as possible rather than waiting. KRA can issue default assessments and apply penalties under the Tax Procedures Act. The exact penalty depends on the return and the circumstances, so check the current position with KRA or a qualified tax adviser. Filing a correct return and keeping the acknowledgement is usually better than leaving the year unfiled.</p>
<h2>Nil return or no return?</h2>
<p>If you are a registered taxpayer who had no income, file the nil return. If you are not registered, a nil return is not the way to register. If you have income but are unsure how to classify it, get advice before choosing nil.</p>
<p><em>This guide is general information, not tax advice. Requirements and deadlines can change. AlbaTech Solutions is an independent service provider and is not KRA.</em></p>
<p><a href="/get-help">Ask AlbaTech Solutions for help organising your KRA return</a>.</p>',
'KRA Nil Return in Kenya: How to File on iTax (2026)',
'Who needs to file a KRA nil return, the current deadline, the iTax steps and the mistakes to avoid. A practical guide for Kenyan taxpayers.',
'published',
'2026-10-23 09:00:00'
);

INSERT IGNORE INTO guides
(title, slug, category, summary, intro, content_html, meta_title, meta_description, status, published_at)
VALUES (
'eTIMS in Kenya: What Businesses Need to Do in 2026',
'etims-kenya-business-guide',
'Tax compliance',
'eTIMS is KRA''s electronic tax invoicing system. Learn who needs to onboard, the available options, what to prepare and how to avoid invoicing problems.',
'eTIMS changes how businesses issue tax invoices: invoices are generated electronically and shared with KRA. Onboarding is a setup task, but the bigger job is building a routine that works every day.',
'<p><strong>eTIMS</strong> stands for electronic Tax Invoice Management System. It is KRA''s system for electronic tax invoicing. Instead of issuing only manual or spreadsheet invoices, businesses generate invoices in a system that transmits invoice data to KRA.</p>
<h2>Who needs to onboard?</h2>
<p>KRA''s published guidance states that persons engaged in business are required to onboard eTIMS and issue electronic tax invoices. The practical requirements depend on your tax profile and the size and nature of your business. Small businesses may have simpler options, and some supplies can use buyer-initiated invoicing.</p>
<p>Do not assume that being below the VAT threshold means eTIMS does not apply. Check your position based on current KRA guidance rather than on what another business told you.</p>
<h2>What are the options?</h2>
<ul>
<li><strong>eTIMS online portal:</strong> issue invoices from a browser without installing separate software.</li>
<li><strong>eTIMS Lite:</strong> KRA''s lighter option, available through web, USSD and a mobile app for smaller businesses.</li>
<li><strong>Integrator or device:</strong> a system connected to your accounting or point-of-sale software, usually through a KRA-approved integrator.</li>
</ul>
<p>KRA states that the eTIMS software is free, while integrators and device providers may charge for their services. Compare what is included before paying for a system you do not need.</p>
<h2>What to prepare</h2>
<ul>
<li>Your KRA PIN and iTax access, with contact details that you control.</li>
<li>Accurate business details: trading name, location, main activity and contact information.</li>
<li>Your VAT or tax status and the type of sales you make.</li>
<li>A sample invoice showing the details you normally include.</li>
<li>A decision on whether you will use the portal, eTIMS Lite or an integrator.</li>
</ul>
<h2>How onboarding works</h2>
<ol>
<li>Sign in to iTax or the eTIMS onboarding page.</li>
<li>Provide the requested business details and select your preferred eTIMS solution.</li>
<li>Complete the registration and note the access details for the chosen system.</li>
<li>Configure your invoice details, including your business name, PIN and the products or services you sell.</li>
<li>Generate a test invoice and check that it is transmitted successfully before you rely on the system.</li>
</ol>
<p>KRA does not require prior approval before you install a solution, but the details you submit must match your tax records.</p>
<h2>Invoicing after setup</h2>
<p>Issue an electronic invoice for each taxable sale, keep the invoice number and share the buyer''s copy. If a sale is cancelled or adjusted, use the credit note process rather than deleting the original invoice. Reconcile the invoices you issue with your sales records so your returns and your eTIMS data tell the same story.</p>
<h2>Common problems</h2>
<ul>
<li><strong>Wrong business or product details</strong> in the invoice setup, which makes records hard to match.</li>
<li><strong>Issuing invoices outside the system</strong> after onboarding, leaving gaps in your records.</li>
<li><strong>Assuming eTIMS replaces tax returns.</strong> It does not. You still file the applicable returns.</li>
<li><strong>Paying for an expensive integrator</strong> before checking whether eTIMS Lite or the portal meets your needs.</li>
<li><strong>Sharing iTax or eTIMS credentials</strong> with someone who does not need access.</li>
</ul>
<h2>Why compliance matters</h2>
<p>KRA is increasing the matching of declared income and expenses against electronic invoice data. Expenses that are not supported by valid electronic tax invoices may be questioned during a review. Building a clean invoicing routine now is easier than reconstructing records later.</p>
<h2>Do you need help?</h2>
<p>If you are unsure which option fits your business, start by confirming your tax profile and the invoices you actually issue. AlbaTech Solutions provides independent setup guidance and can help you prepare the information before you onboard.</p>
<p><em>This guide is general information, not tax advice. Requirements and system options can change. AlbaTech Solutions is an independent service provider and is not KRA. Never share your iTax password, PIN or one-time verification codes.</em></p>
<p><a href="/get-help">Ask about eTIMS setup assistance</a>.</p>',
'eTIMS Registration in Kenya: Business Guide (2026)',
'Who needs eTIMS, the online portal, eTIMS Lite and integrator options, what to prepare and the invoicing mistakes Kenyan businesses should avoid.',
'published',
'2026-10-23 10:00:00'
);

INSERT IGNORE INTO guides
(title, slug, category, summary, intro, content_html, meta_title, meta_description, status, published_at)
VALUES (
'BRS Annual Returns in Kenya: Deadlines, Penalties and Steps',
'brs-annual-returns-kenya',
'Business registration',
'Every registered company must file annual returns with the Registrar. Learn what the return contains, when it is due, what happens when it is late and how to file through BRS.',
'An annual return is a compliance filing, not a tax return. It keeps the Registrar''s record of your company up to date and helps you avoid penalties, strike-off risk and problems when you need a CR12 or a tender document.',
'<p>An <strong>annual return</strong> is a statement filed with the Registrar of Companies through the Business Registration Service (BRS). It records key details about the company for the relevant year, such as its directors, shareholders and registered office. It is separate from the tax returns you file with KRA.</p>
<h2>Which companies must file?</h2>
<p>Companies registered under the Companies Act are required to lodge annual returns every year. Limited liability partnerships have a similar filing obligation under their own Act. A company does not stop needing to file simply because it is dormant, made no sales or has no employees. If the company is still on the register, assume a return is due unless BRS confirms otherwise.</p>
<h2>When is the return due?</h2>
<p>The Companies Act requires annual returns to be lodged with the Registrar each year. In practice, BRS expects the return within 30 days after the anniversary of the company''s incorporation or registration. Confirm your due date in the BRS portal rather than relying on a reminder from someone else.</p>
<p>Do not confuse the annual return with your KRA income tax return. The BRS deadline, the information required and the consequences of late filing are different.</p>
<h2>What information you need</h2>
<ul>
<li>The company name and registration number.</li>
<li>The latest CR12 or current director and shareholder records.</li>
<li>Shareholding details, including the number and class of shares.</li>
<li>The registered office address and company secretary details where applicable.</li>
<li>Beneficial ownership information where the company is required to maintain it.</li>
<li>The financial year or return year being filed.</li>
</ul>
<h2>How to file through BRS</h2>
<ol>
<li>Sign in to eCitizen and open the Business Registration Service.</li>
<li>Select the company you want to maintain.</li>
<li>Choose the annual returns option and select the year you are filing.</li>
<li>Check the pre-filled details and update anything that has changed, such as directors, shareholders or the registered office.</li>
<li>Submit the return and pay any applicable BRS fee.</li>
<li>Download and keep the acknowledgement and the updated records.</li>
</ol>
<p>Menu labels and fees can change. If a detail in the portal does not match your records, correct it before submitting rather than filing inaccurate information.</p>
<h2>What happens if you file late</h2>
<p>Late or missing annual returns can attract penalties, and prolonged non-filing puts the company at risk of being struck off the register. Directors can also face offences under the Companies Act for failing to lodge returns. A company that is not in good standing may run into problems when applying for tenders, opening or maintaining bank accounts, or trying to obtain a CR12.</p>
<p>If returns are already overdue, review the filing history first, then file the outstanding years in the correct order. Do not assume that filing the current year fixes earlier gaps.</p>
<h2>Annual returns vs other filings</h2>
<ul>
<li><strong>KRA returns:</strong> income tax, VAT, PAYE and other tax filings, handled through iTax.</li>
<li><strong>Annual returns:</strong> company information filed with the Registrar through BRS.</li>
<li><strong>Beneficial ownership:</strong> a separate register and filing obligation for many companies.</li>
<li><strong>County permits:</strong> business permits issued by the county where you operate.</li>
</ul>
<h2>Common mistakes</h2>
<ul>
<li>Ignoring the return because the company is dormant or made no profit.</li>
<li>Filing with old director or shareholder details.</li>
<li>Confusing a KRA nil return with a BRS annual return.</li>
<li>Waiting until a tender or bank request forces a rushed filing.</li>
<li>Losing the acknowledgement and having no evidence of filing.</li>
</ul>
<h2>A simple compliance routine</h2>
<p>Put the incorporation anniversary and the KRA filing dates in your calendar. Review director and shareholder records once a year, keep the latest CR12, and file before the deadline rather than after a reminder. If you are unsure what is outstanding, start with a filing-status review.</p>
<p><em>This guide is general information and is not legal advice. Requirements, fees and penalties can change. AlbaTech Solutions is an independent service provider and is not the Business Registration Service.</em></p>
<p><a href="/get-help">Ask AlbaTech Solutions to review your annual return status</a>.</p>',
'BRS Annual Returns in Kenya: Deadlines and Steps',
'What a BRS annual return contains, when it is due, how to file it for a Kenyan company and what happens when returns are filed late.',
'published',
'2026-10-23 11:00:00'
);
