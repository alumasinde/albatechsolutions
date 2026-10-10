<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;

final class GuideController
{
    /** @return array<string, array<string, mixed>> */
    public static function articles(): array
    {
        $articles = [
            'how-much-does-a-website-cost-in-kenya' => [
                'title' => 'How Much Does a Website Cost in Kenya?',
                'meta_title' => 'Website Design Cost in Kenya: What to Budget',
                'meta_description' => 'Learn what affects website design costs in Kenya, what to budget for, and which questions to ask before hiring a web designer.',
                'summary' => 'Understand the main factors behind website pricing and how to compare quotes without focusing on price alone.',
                'category' => 'Websites',
                'published' => '2026-10-09',
                'updated' => '2026-10-09',
                'intro' => 'There is no single price for every website. The cost depends on what the site needs to do, how much content it contains, and whether it needs custom functionality.',
                'sections' => [
                    ['heading' => 'What affects the price?', 'paragraphs' => ['The number of pages, the complexity of the design, mobile responsiveness, copywriting, image preparation, forms, booking features and integrations can all affect the amount of work involved.', 'A simple informational website is usually a different project from an online store, a booking platform or a custom business system. Define the outcome before comparing quotes.']],
                    ['heading' => 'Budget for more than the build', 'paragraphs' => ['Ask whether the quote includes domain registration, hosting, SSL, business email, content entry, training, backups and ongoing maintenance. Some of these are recurring costs, while others may be one-off tasks.', 'Confirm who owns the domain, hosting account, website files and administrator credentials when the project is complete.']],
                    ['heading' => 'Questions to ask before accepting a quote', 'bullets' => ['What pages and features are included?', 'Will the website work well on phones and tablets?', 'Who provides and approves the written content and images?', 'What is the delivery timeline, and what could delay it?', 'What support is included after launch?', 'Which costs recur annually or monthly?']],
                    ['heading' => 'How to get an accurate quote', 'paragraphs' => ['Write down the purpose of the website, the pages you need, your preferred examples, and the action visitors should take. A clear brief helps a provider quote the same scope you have in mind.', 'AlbaTech Solutions can discuss your requirements and explain the available options before you decide. Visit the service catalogue to explore website-related services.'], 'link' => ['/services', 'Explore available services']],
                ],
            ],
            'domain-and-hosting-explained' => [
                'title' => 'Domain and Hosting Explained for Small Businesses',
                'meta_title' => 'Domain vs Hosting: A Simple Guide for Kenya',
                'meta_description' => 'Learn the difference between a domain name and web hosting, what each does, and what to check before paying for a website.',
                'summary' => 'A plain-language guide to the two essentials that help your website have an address and stay online.',
                'category' => 'Websites',
                'published' => '2026-10-09',
                'updated' => '2026-10-09',
                'intro' => 'A domain name is the address people type to find your website. Hosting is the service that stores the website files and makes them available online. Most business websites need both.',
                'sections' => [
                    ['heading' => 'What is a domain name?', 'paragraphs' => ['A domain name is your website address, such as yourbusiness.co.ke. Choose a name that is easy to say, spell and associate with your business.', 'Register the domain using an account you control, and keep the renewal date and access details safe. A domain registration is normally renewed periodically.']],
                    ['heading' => 'What is web hosting?', 'paragraphs' => ['Hosting stores the files, images and application code used by your website. Hosting plans differ in storage, performance, email features, backups, support and the software they can run.', 'Choose hosting based on the needs of your website, not only the lowest advertised price. Ask how backups work and how you can recover the site if something goes wrong.']],
                    ['heading' => 'What about SSL and business email?', 'paragraphs' => ['An SSL/TLS certificate enables HTTPS, which encrypts connections between the visitor and your website. HTTPS should be configured before a public business website launches.', 'Business email can use your domain, but email hosting may be included with a hosting plan or purchased separately. Check storage, spam protection and how accounts are managed.']],
                    ['heading' => 'A quick checklist before you pay', 'bullets' => ['Confirm the exact domain spelling and who will own the account.', 'Check the renewal price, not only the first-term promotion.', 'Ask about HTTPS, backups, support and restoration.', 'Confirm whether email accounts are included.', 'Keep invoices and account recovery details accessible.']],
                ],
            ],
            'what-to-prepare-before-building-a-business-website' => [
                'title' => 'What to Prepare Before Building a Business Website',
                'meta_title' => 'Business Website Checklist: What to Prepare First',
                'meta_description' => 'Use this practical checklist to prepare your business details, services, photos, contact information and goals before website development.',
                'summary' => 'Prepare the key information that helps a designer build a useful website with fewer delays.',
                'category' => 'Business presence',
                'published' => '2026-10-09',
                'updated' => '2026-10-09',
                'intro' => 'A website project is easier when the business owner can provide clear information early. You do not need perfect copy, but you should know what the business offers and how customers should contact you.',
                'sections' => [
                    ['heading' => 'Start with the goal', 'paragraphs' => ['Decide what the website should help visitors do: call, request a quote, visit a location, book a service, read information or buy a product. This decision guides the page structure and calls to action.']],
                    ['heading' => 'Prepare your business information', 'bullets' => ['Business name and a short description.', 'A list of services or products, with accurate descriptions.', 'Public phone number, email, WhatsApp and service area.', 'Opening hours or response expectations, if relevant.', 'Real photos, logo files and permission to use any supplied images.', 'Customer questions you answer frequently.']],
                    ['heading' => 'Plan the pages', 'paragraphs' => ['Many small businesses start with Home, About, Services, Contact and a privacy notice where applicable. Add pages only when they help visitors understand your offer or take the next step.', 'Use plain language. Explain who the service is for, what is included, what the customer needs to provide and how to enquire.']],
                    ['heading' => 'Avoid common delays', 'bullets' => ['Assign one person to approve content and design feedback.', 'Make sure you control the domain and hosting accounts.', 'Agree on the scope, delivery stages and payment terms.', 'Provide real business details instead of placeholder text.', 'Test every contact form and phone link before launch.']],
                    ['heading' => 'Ready to discuss your website?', 'paragraphs' => ['Bring your goals and any existing materials to an initial discussion. You can review the available options and agree on a scope before work begins.'], 'link' => ['/get-help', 'Contact AlbaTech Solutions']],
                ],
            ],
            'local-online-visibility-checklist' => [
                'title' => 'Online Business Presence: A Practical Local Visibility Checklist',
                'meta_title' => 'Local Online Visibility Checklist for Kenyan Businesses',
                'meta_description' => 'Practical steps for improving a business presence online with consistent contact details, useful pages, real photos and customer information.',
                'summary' => 'Simple, sustainable ways to make it easier for customers to find accurate information about your business online.',
                'category' => 'Online presence',
                'published' => '2026-10-09',
                'updated' => '2026-10-09',
                'intro' => 'A strong online presence is more than a website. Customers should be able to find accurate information, understand what you offer and contact you without confusion.',
                'sections' => [
                    ['heading' => 'Keep business information consistent', 'paragraphs' => ['Use the same business name, phone number, email and service area across your website and the profiles you manage. Correct outdated details promptly.', 'If your business serves customers at a physical location, publish an address only when it is appropriate and accurate. Service-area businesses should describe their actual coverage instead of implying they have an office they do not operate.']],
                    ['heading' => 'Make your website useful', 'bullets' => ['Describe services in customer-friendly language.', 'Provide a clear way to enquire or request a quote.', 'Make the site usable on mobile phones.', 'Use real, relevant images with permission.', 'Keep hours, pricing labels and service availability accurate.']],
                    ['heading' => 'Use business profiles carefully', 'paragraphs' => ['If you use a local business listing platform, follow its current eligibility and verification rules. Keep access under accounts controlled by the business, and never create misleading locations or reviews.', 'Avoid promising a particular search ranking. Search visibility depends on many factors and cannot be guaranteed by a listing or website alone.']],
                    ['heading' => 'Measure what helps customers', 'paragraphs' => ['Track meaningful enquiries, calls and requests rather than focusing only on visitor counts. Review common customer questions and improve the information that is missing or unclear.']],
                ],
            ],
            'business-registration-assistance-what-to-prepare' => [
                'title' => 'Business Registration Assistance in Kenya: What to Prepare',
                'meta_title' => 'Business Registration in Kenya: Preparation Checklist',
                'meta_description' => 'A general preparation checklist for people exploring business registration in Kenya, including details to organise and official sources to confirm.',
                'summary' => 'Organise your information before starting a business registration process and know what to verify through official channels.',
                'category' => 'Business guidance',
                'published' => '2026-10-09',
                'updated' => '2026-10-09',
                'intro' => 'Before starting a business registration process, organise the details that may be requested and confirm the current requirements through the relevant official service. Requirements can differ depending on the business structure and circumstances.',
                'sections' => [
                    ['heading' => 'Information to organise', 'bullets' => ['Potential business names to check for availability.', 'The proposed business activity and contact details.', 'Names and identification details for the relevant owners or officers, where required.', 'A reliable email address and phone number.', 'Any supporting information requested by the official process.']],
                    ['heading' => 'Verify requirements and charges', 'paragraphs' => ['Use the current official government service to confirm eligibility, required documents, fees and processing steps before submitting information or paying. Do not rely on an old checklist if official requirements have changed.', 'Keep copies of receipts, reference numbers and submitted documents. Never share passwords, one-time codes or account recovery codes with a service provider.']],
                    ['heading' => 'Understand independent assistance', 'paragraphs' => ['An independent service provider may help you understand a process, organise information or complete agreed administrative tasks. It is not a government agency and cannot guarantee that an application will be accepted or approved.', 'The relevant authority controls official decisions and processing. Always review the information before submission and make sure it is accurate.']],
                    ['heading' => 'Need help preparing?', 'paragraphs' => ['You can ask AlbaTech Solutions about the scope of available business registration assistance. The work, charges and responsibilities should be agreed before anything is submitted.'], 'link' => ['/services', 'View the service catalogue']],
                ],
            ],
        ];
        // Published database-managed guides override/add to the original editorial catalogue.
        try {
            $rows = \AlbaTech\Core\Database::connection()->query("SELECT * FROM guides ORDER BY published_at DESC, updated_at DESC")->fetchAll();
            foreach ($rows as $row) {
                unset($articles[(string)$row['slug']]);
                if ($row['status'] !== 'published') continue;
                $articles[(string)$row['slug']] = [
                    'title' => $row['title'],
                    'meta_title' => $row['meta_title'] ?: $row['title'],
                    'meta_description' => $row['meta_description'] ?: $row['summary'],
                    'summary' => $row['summary'],
                    'category' => $row['category'],
                    'published' => !empty($row['published_at']) ? date('Y-m-d', strtotime($row['published_at'])) : '',
                    'updated' => !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : '',
                    'intro' => $row['intro'],
                    'content_html' => $row['content_html'],
                    'sections' => [],
                ];
            }
        } catch (\Throwable $e) {
            // The original static guides remain available if migration 008 has not yet run.
            error_log('[AlbaTech guides] Database-managed guides unavailable: ' . $e->getMessage());
        }
        return $articles;
    }

    public function index(): void
    {
        $articles = self::articles();
        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guides', [
            'pageTitle' => 'Practical Business & Website Guides in Kenya | AlbaTech Solutions',
            'metaDescription' => 'Straightforward guides on website costs, domains and hosting, business website preparation, online visibility and business registration assistance in Kenya.',
            'canonicalUrl' => $baseUrl . '/guides',
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
            'guideArticles' => $articles,
        ]));
    }

    public function show(\AlbaTech\Core\Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $articles = self::articles();
        $article = $articles[$slug] ?? null;

        if (!is_array($article)) {
            Response::html(View::render('errors/404', [
                'pageTitle' => 'Guide not found | AlbaTech Solutions',
                'metaRobots' => 'noindex, nofollow',
                'canonicalUrl' => '',
            ]), 404);
            return;
        }

        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guide-article', [
            'pageTitle' => $article['meta_title'] . ' | AlbaTech Solutions',
            'metaDescription' => $article['meta_description'],
            'canonicalUrl' => $baseUrl . '/guides/' . rawurlencode($slug),
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
            'guideArticle' => $article,
            'guideSlug' => $slug,
        ]));
    }
}
