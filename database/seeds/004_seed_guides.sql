-- AlbaTech Solutions guide seed data.
-- Run after database/migrations/008_guides_cms.sql has been applied.
-- INSERT IGNORE makes this safe to re-run without overwriting edits made in Admin > Guides & Articles.
-- All four articles are fully written, published seed content (not placeholder copy).

INSERT IGNORE INTO guides
(title, slug, category, summary, intro, content_html, meta_title, meta_description, status, published_at, created_by, updated_by)
VALUES
('How Much Does a Website Cost in Kenya?', 'how-much-does-a-website-cost-in-kenya', 'Website design', 'Planning a business website in Kenya? Learn what drives the price, which ongoing costs to budget for, and how to compare quotations fairly before paying a developer.', 'Website design prices in Kenya vary because a simple online brochure and a custom booking or e-commerce system are very different projects. Start with the job your website must do, then compare quotes against the same list of pages, features and support.', '<p><strong>Short answer:</strong> the cost of a website in Kenya depends on its size, design, content, functionality and ongoing support. A small business website with a few clear pages requires less work than an online shop, booking platform or custom web application. Ask providers to quote for the same scope so you can compare value fairly.</p>
<h2>What affects website design prices?</h2>
<h3>1. Number of pages and amount of content</h3>
<p>A website with Home, About, Services and Contact pages is a different undertaking from one with dozens of service pages, a blog, a portfolio or multiple locations. Writing, editing and uploading content also take time. Confirm whether you will supply final copy or the developer will help prepare it.</p>
<h3>2. Features and integrations</h3>
<p>Contact forms, WhatsApp links and a simple enquiry flow are relatively straightforward compared with customer accounts, online payments, inventory, appointment scheduling or integration with another system. List the features you genuinely need for launch and separate future improvements from must-haves.</p>
<h3>3. Design, mobile usability and accessibility</h3>
<p>A business website should be easy to use on a phone, load reliably on ordinary mobile connections and make key actions obvious. Ask how the developer will test mobile layouts, forms, navigation, keyboard access and page speed, not just how the homepage looks on a laptop.</p>
<h2>Budget for the costs after launch</h2>
<p>The initial build is only one part of owning a website. Depending on the arrangement, you may also pay for:</p>
<ul>
<li><strong>Domain registration:</strong> the address people type to visit your site, such as a .co.ke domain. Check the renewal price and who controls the registrar account.</li>
<li><strong>Web hosting:</strong> the service that keeps your website files and application available online.</li>
<li><strong>Business email:</strong> domain-based email may be included with hosting or billed separately.</li>
<li><strong>Maintenance and backups:</strong> updates, security checks, monitoring and help restoring the site if something goes wrong.</li>
<li><strong>Extra work:</strong> new pages, new features, content changes or third-party services outside the agreed scope.</li>
</ul>
<p>Ask which costs are one-off and which recur monthly or annually. Get the renewal amounts in writing rather than relying only on a first-year promotion.</p>
<h2>How to compare website quotations</h2>
<p>Two quotations are only comparable when they describe roughly the same outcome. Before choosing, ask each provider:</p>
<ul>
<li>Which pages and features are included, and what is excluded?</li>
<li>Will the site be responsive and tested on mobile phones?</li>
<li>Who supplies the text, images and logo, and who has permission to use them?</li>
<li>Are basic SEO settings, analytics setup, HTTPS and sitemap generation included?</li>
<li>Who owns the domain, hosting account, source code and administrator access?</li>
<li>What is the delivery schedule, how many revision rounds are included, and what support follows launch?</li>
<li>What happens if you later want to move to another provider?</li>
</ul>
<h2>A simple brief that helps you get an accurate quote</h2>
<p>Write down what your business does, who you want to reach, the pages you need, examples of websites you like and the action visitors should take: call, WhatsApp, request a quote, book or buy. Include any deadline and a realistic starting budget if you have one. This helps a developer recommend an appropriate scope instead of guessing.</p>
<h2>Watch out for these warning signs</h2>
<ul>
<li>A promise of guaranteed Google rankings or a guaranteed number of customers.</li>
<li>No written list of deliverables, costs, payment stages or ownership arrangements.</li>
<li>Pressure to register the domain or hosting in an account you cannot access.</li>
<li>A very low quote that leaves essential items such as content, mobile testing, backups or launch support undefined.</li>
</ul>
<h2>What should you do next?</h2>
<p>You do not need to buy every possible feature at once. Start with the pages and actions that will help customers understand your offer and contact you. Add more functionality when there is a clear business reason for it.</p>
<p><a href="/get-help">Talk to AlbaTech Solutions about your website requirements</a> and agree on the scope and costs before work begins.</p>', 'Website Design Cost in Kenya: What to Budget', 'Understand website design costs in Kenya, domain and hosting fees, features that affect pricing, and questions to ask before hiring a web designer.', 'published', '2026-10-10 09:00:00', NULL, NULL),
('Domain and Hosting Explained for Small Businesses', 'domain-and-hosting-explained', 'Websites', 'Understand the difference between a domain name, web hosting, HTTPS and business email, plus the checks to make before buying a website package in Kenya.', 'Your domain is your website''s address; hosting is where the website runs. They work together, but they are different services. Knowing who controls each account helps protect your business and makes future changes easier.', '<p>A <strong>domain name</strong> is the address customers use to find you online. <strong>Web hosting</strong> stores the files and software that make your website work. Most websites need both, but they do not have to be purchased from the same company.</p>
<h2>What is a domain name?</h2>
<p>A domain is the human-friendly address for your website, for example <code>yourbusiness.co.ke</code>. A .co.ke address can make sense for a business serving customers in Kenya, while other domain endings may suit a different audience or brand. Choose a name that is easy to say, spell and remember.</p>
<p>Register the domain in an account controlled by you or your business. Keep the login details secure, use a recovery email you can access, enable multi-factor authentication where available and set reminders for renewal. If a developer registers it on your behalf, confirm that you are the registrant or have clear ownership and transfer rights.</p>
<h2>What is web hosting?</h2>
<p>Hosting is the service that stores your website''s code, pages, images and other files and serves them to visitors. Hosting plans can differ in performance, storage, backups, email, technical support and the technologies they support.</p>
<p>Choose a plan that matches the website you are building. A basic informational site and an online shop with frequent orders have different needs. Ask what happens when traffic grows, whether backups run automatically, how long they are retained and how restoration works. A backup is only useful if it can be restored.</p>
<h2>Where does HTTPS fit in?</h2>
<p>HTTPS encrypts the connection between a visitor''s browser and your website. It is important for privacy and helps browsers establish a secure connection. Before launch, confirm that the site uses HTTPS, that pages do not load important assets over insecure HTTP and that visitors are redirected to the preferred secure URL.</p>
<h2>What about business email?</h2>
<p>An address such as <code>info@yourbusiness.co.ke</code> uses your domain, but the mailbox is provided by an email-hosting service. Email may be bundled with web hosting or provided separately. Check mailbox storage, spam filtering, account recovery, mobile setup and whether the service supports the protocols your devices need.</p>
<h2>Questions to ask before paying</h2>
<ul>
<li>Who will own and control the domain registrar account?</li>
<li>What is the renewal cost for the domain and hosting after the first term?</li>
<li>Is HTTPS included, and who renews or maintains the certificate?</li>
<li>Are business email accounts included? If so, how many and with what storage?</li>
<li>How often are backups made, where are they stored and how can a restore be requested?</li>
<li>Can you obtain a copy of your website files and database if you change providers?</li>
<li>What support is included, and how do you contact the provider when the site is down?</li>
</ul>
<h2>Keep your accounts separate and secure</h2>
<p>Use unique passwords and a password manager where possible. Do not send passwords or one-time codes in ordinary messages. Give a developer only the access needed for the work, and remove access that is no longer required. Keep invoices, renewal dates and recovery information somewhere secure.</p>
<h2>Before your website goes live</h2>
<p>Test the domain on both mobile and desktop, check the HTTPS padlock and forms, verify business email sending and receiving, and confirm that you can access the domain and hosting dashboards. These small checks can prevent expensive confusion later.</p>
<p><a href="/get-help">Ask AlbaTech Solutions about domain, hosting and website setup</a> before choosing a package.</p>', 'Domain vs Hosting: A Simple Guide for Kenya', 'Learn what a domain name and web hosting do, how HTTPS and business email fit in, and what Kenyan small businesses should check before paying.', 'published', '2026-10-10 09:00:00', NULL, NULL),
('What to Prepare Before Building a Business Website', 'what-to-prepare-before-building-a-business-website', 'Business presence', 'Use this practical website checklist to organise your business details, services, photos, contact options and goals before hiring a web designer in Kenya.', 'You do not need to arrive with a perfect website plan. A clear description of your business, the customers you want to reach and the action you want them to take is enough to start a useful conversation and reduce delays.', '<p>The most useful preparation for a business website is not a long technical document. It is a clear answer to three questions: what does your business offer, who needs it, and what should a visitor do next?</p>
<h2>1. Decide what the website should achieve</h2>
<p>Choose the main action you want visitors to take. It might be calling your business, starting a WhatsApp conversation, requesting a quotation, booking an appointment, visiting your premises or buying a product. The goal affects the page layout, wording and features.</p>
<p>Be specific about the customers you serve. For example, say whether you work in a particular town, serve customers across Kenya or offer services remotely. Publish only locations and availability that are accurate.</p>
<h2>2. Gather your business information</h2>
<ul>
<li>Your business name and a short, plain-language description.</li>
<li>A list of services or products, with a sentence explaining each one.</li>
<li>Your public phone number, business email and preferred enquiry channel.</li>
<li>Your service area and opening hours, if relevant.</li>
<li>Your logo in the best quality file you have.</li>
<li>Real photographs of your work, team or products, with permission to publish them.</li>
<li>Frequently asked questions, common customer concerns and any genuine testimonials you have permission to use.</li>
</ul>
<p>If a detail is not confirmed yet, tell the designer instead of publishing placeholder text. It is better to launch with a smaller amount of accurate information than a full website containing guesses.</p>
<h2>3. Plan the pages visitors actually need</h2>
<h3>Home</h3>
<p>Explain what you do, who you help and how a visitor can take the next step. Avoid making people scroll through a long company history before they understand the offer.</p>
<h3>Services or products</h3>
<p>Describe what is included, who each offer suits, what the customer needs to provide and how to ask for a price. If prices depend on the scope, explain that clearly rather than displaying an invented figure.</p>
<h3>About and contact</h3>
<p>Share relevant background, how you work and the best way to reach you. Keep contact information consistent across the website and the profiles you control.</p>
<h3>Other pages</h3>
<p>Add a portfolio, FAQs, booking page, online shop or articles when they answer a real customer need. Every extra feature adds work to build, test and maintain, so prioritise what matters for launch.</p>
<h2>4. Prepare photos and written content</h2>
<p>Use images you own or have permission to use. Choose clear, relevant photos rather than random stock images that do not represent your business. Provide descriptive image filenames and tell the designer what each image shows. For longer pages, organise copy with clear headings, short paragraphs and useful lists.</p>
<h2>5. Agree on scope, cost and ownership</h2>
<p>Before work starts, confirm the included pages and features, what is excluded, who supplies content, how revisions work, the expected timeline, payment stages and post-launch support. Ask who controls the domain, hosting account, source code and administrator login. Keep the agreement and receipts.</p>
<h2>6. Check the website before launch</h2>
<ul>
<li>Open every page on a phone and a computer.</li>
<li>Submit the contact form and confirm the message reaches the right inbox.</li>
<li>Test WhatsApp, telephone and email links.</li>
<li>Check spelling, service details, prices and contact information.</li>
<li>Confirm HTTPS works and that the site has no obvious broken links.</li>
<li>Ask how updates, backups and support will be handled.</li>
</ul>
<h2>A practical way to get started</h2>
<p>Write a short brief with your business name, services, target customers, preferred contact method and examples of websites you like. That is enough to discuss a sensible first version. You can improve the website over time as you learn what customers ask for.</p>
<p><a href="/get-help">Contact AlbaTech Solutions to discuss your website brief</a> and agree on the next step before development begins.</p>', 'Business Website Checklist: What to Prepare First', 'Preparing a business website in Kenya? Use this checklist for business details, services, photos, contact information, website goals and launch readiness.', 'published', '2026-10-10 09:00:00', NULL, NULL),
('How Kenyan Small Businesses Can Improve Local Online Visibility', 'local-online-visibility-checklist', 'Online presence', 'A practical local SEO checklist for Kenyan businesses: keep contact details consistent, improve service pages, manage business listings and make it easy for customers to enquire.', 'When someone searches for a service nearby, they need reliable information: what you do, where you serve, how to reach you and whether you are a good fit. A useful online presence starts by answering those questions accurately.', '<p>Local online visibility is not just about ranking for a keyword. It is about helping the right customers find accurate details and decide whether to contact your business. You can improve the basics without making unrealistic promises about where your website will appear on Google.</p>
<h2>1. Make your business details consistent</h2>
<p>Use the same business name, phone number, website address and service-area description on your website and the business profiles you manage. Correct old phone numbers, broken links and outdated opening hours. If you do not serve customers at a public storefront, do not publish a misleading address just to appear local.</p>
<h2>2. Create useful pages for your services</h2>
<p>Give each important service a clear page when there is enough useful information to justify one. Explain what the service includes, who it is for, how the process works, what information the customer should prepare and how to request a quote. Avoid creating many near-identical pages that only swap the name of a town.</p>
<p>Use the words your customers naturally use, including relevant service and location terms where they fit. A clear title and heading help visitors understand the page, but repeating a keyword in every paragraph makes the writing harder to read.</p>
<h2>3. Review your local business profile eligibility</h2>
<p>If your business qualifies for a Google Business Profile or another directory, claim and manage it using an account controlled by the business. Follow the platform''s current rules for service-area businesses, address visibility, categories and verification. Keep profile details accurate and update them when your services or contact information change.</p>
<p>Do not create fake locations, use an address where you are not eligible to represent the business, or pay for fabricated reviews. These shortcuts can damage trust and may violate platform policies.</p>
<h2>4. Make the website work well on phones</h2>
<p>Many customers will visit from a mobile phone. Make sure text is readable, buttons are easy to tap, pages do not scroll sideways and the main contact option is easy to find. Compress images, remove unnecessary scripts and test the website on a real phone and a slower connection.</p>
<p>Check that WhatsApp, phone and email links work as intended. If you use an enquiry form, test that submissions reach the correct inbox and that the visitor sees a clear confirmation.</p>
<h2>5. Use genuine proof of your work</h2>
<p>Publish real project screenshots, product photos, completed work or testimonials only when you have permission to share them. Explain what you actually did and avoid claiming results you cannot verify. A few specific examples are more persuasive than broad statements about being the best.</p>
<h2>6. Ask for honest reviews and respond professionally</h2>
<p>When a customer has a genuine experience with your business, you can invite them to leave an honest review on a platform they use. Do not offer rewards in exchange for positive ratings, write reviews on behalf of customers or pressure people to change critical feedback. Respond calmly and protect customer privacy when resolving a complaint.</p>
<h2>7. Measure enquiries, not just traffic</h2>
<p>Track meaningful actions such as calls, form submissions, WhatsApp conversations and quote requests. Review which pages help people take the next step and which questions remain unanswered. Use analytics and Search Console where appropriate, while respecting privacy and applicable consent requirements.</p>
<h2>A monthly local visibility checklist</h2>
<ul>
<li>Check your phone, email, website URL and opening hours on the profiles you manage.</li>
<li>Test contact forms and WhatsApp links from a phone.</li>
<li>Review important service pages for accuracy and missing customer information.</li>
<li>Fix broken links and replace outdated images or offers.</li>
<li>Respond to genuine customer feedback without exposing private details.</li>
<li>Review search queries and enquiries to learn what customers need next.</li>
</ul>
<h2>Keep expectations realistic</h2>
<p>Local search visibility changes with competition, relevance, distance, platform rules and many other factors. No website or listing can guarantee a specific position. Consistent information, useful content and a reliable customer experience give people stronger reasons to trust and contact your business.</p>
<p><a href="/get-help">Talk to AlbaTech Solutions about improving your online presence</a> and decide which practical steps make sense for your business.</p>', 'Local SEO in Kenya: Online Visibility Checklist', 'Improve local online visibility in Kenya with consistent business details, useful service pages, an eligible business profile, customer reviews and mobile-friendly contact options.', 'published', '2026-10-10 09:00:00', NULL, NULL);
