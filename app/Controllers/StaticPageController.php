<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Repositories\SiteSettingsRepository;
use Throwable;

final class StaticPageController
{
    public function about(): void
    {
        $settings = (new SiteSettingsRepository())->publicValues();
        $this->render('about', 'pages/about', 'About AlbaTech Solutions | Digital Services in Nairobi, Kenya', 'Learn how AlbaTech Solutions helps businesses and individuals across Kenya with practical website design, business registration support and online visibility.', 'about', [
            'schemaPageType' => 'AboutPage',
            'founderName' => trim((string) ($settings['founder_name'] ?? '')),
            'founderTitle' => trim((string) ($settings['founder_title'] ?? '')),
            'founderBio' => trim((string) ($settings['founder_bio'] ?? '')),
            'founderLinkedin' => trim((string) ($settings['founder_linkedin'] ?? '')),
        ], ['name' => 'About', 'url' => '/about']);
    }

    public function getHelp(): void
    {
        $this->render('get-help', 'pages/get-help', 'Get Help with a Website or Business Service in Kenya', 'Send an enquiry or start a WhatsApp chat about website design, business registration, KRA returns and other digital services in Kenya.', 'get-help', [
            'csrf' => AdminSecurity::csrfToken(),
            'enquiryServices' => (new \AlbaTech\Repositories\ServiceRepository())->enquiryOptions(),
            'schemaPageType' => 'ContactPage',
            'faqItems' => [
                ['question' => 'What is the fastest way to reach AlbaTech?', 'answer' => 'WhatsApp or a phone call during business hours is usually fastest. You can also submit the enquiry form; a phone number is all we need to reply, and email is optional.'],
                ['question' => 'Do I need to share documents before getting a quote?', 'answer' => 'No. Start with a short description of the task. Once the scope is agreed, we tell you exactly which documents are needed. Never send passwords, PINs, one-time verification codes or M-Pesa credentials.'],
                ['question' => 'What does an enquiry cost?', 'answer' => 'Sending an enquiry is free and there is no obligation. We confirm any charges in writing before work begins.'],
                ['question' => 'Is AlbaTech Solutions a government agency?', 'answer' => 'No. AlbaTech Solutions is an independent service provider. Government fees are paid to the relevant authority, and decisions, requirements and processing times remain with that authority.'],
            ],
        ], ['name' => 'Contact', 'url' => '/get-help']);
    }
    public function contactDetails(): void { $this->render('contact-details', 'pages/contact-details', 'Contact & Location | AlbaTech Solutions Kenya', 'WhatsApp, phone and email details for AlbaTech Solutions in Nairobi, with nationwide remote support across Kenya.', 'contact-details', [], ['name' => 'Contact Details', 'url' => '/contact-details']); }
    public function terms(): void { $this->render('terms', 'pages/terms', 'Terms of Service', 'Read the terms that apply when using AlbaTech Solutions services and website.', 'terms', [], ['name' => 'Terms', 'url' => '/terms']); }
    public function privacy(): void { $this->render('privacy', 'pages/privacy', 'Privacy Policy', 'Learn how AlbaTech Solutions handles enquiries and personal information.', 'privacy', [], ['name' => 'Privacy', 'url' => '/privacy']); }
    public function dataDeletion(): void { $this->render('data-deletion', 'pages/data-deletion', 'Data Deletion Instructions', 'How to request deletion of personal information held by AlbaTech Solutions, including enquiry and WhatsApp conversation data.', 'data-deletion', [], ['name' => 'Data deletion', 'url' => '/data-deletion']); }

    private function render(string $slug, string $fallbackView, string $title, string $description, string $canonicalSlug, array $extra = [], array $breadcrumbItem = []): void
    {
        $baseUrl = SiteUrl::base();
        $page = null;
        try { $stmt = Database::connection()->prepare('SELECT * FROM cms_pages WHERE slug=:slug LIMIT 1'); $stmt->execute(['slug' => $slug]); $page = $stmt->fetch(); } catch (Throwable $e) { error_log('[AlbaTech CMS page] ' . $e->getMessage()); }
        if (is_array($page) && ($page['status'] ?? '') !== 'published') {
            Response::html(View::render('errors/404', ['pageTitle' => 'Page not found | AlbaTech Solutions', 'metaRobots' => 'noindex, nofollow', 'canonicalUrl' => '']), 404);
            return;
        }
        $breadcrumbs = [['name' => 'Home', 'url' => '/']];
        if (!empty($breadcrumbItem)) {
            $breadcrumbs[] = $breadcrumbItem;
        }
        if ($page && $slug !== 'get-help' && mb_strlen(trim((string) ($page['body_content'] ?? ''))) >= 300) {
            // Shorter bodies are the one-line seed placeholders; those pages keep their designed templates.
            Response::html(View::render('pages/cms-static', [
                'pageTitle' => SeoDefaults::title((string)($page['meta_title'] ?? ''), (string)($page['title'] ?? $title) . ' | AlbaTech Solutions'),
                'metaDescription' => SeoDefaults::description((string)($page['meta_description'] ?? ''), (string)($page['intro'] ?? '') ?: $description),
                'metaImage' => SeoDefaults::image((string)($page['social_image'] ?? ''), $baseUrl),
                'canonicalUrl' => $baseUrl . '/' . $canonicalSlug,
                'cmsPage' => $page,
                'breadcrumbs' => $breadcrumbs,
            ] + $extra));
            return;
        }
        $data = [
            'pageTitle' => SeoDefaults::title((string)($page['meta_title'] ?? ''), $title . ' | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description((string)($page['meta_description'] ?? ''), (string)($page['intro'] ?? '') ?: $description),
            'metaImage' => SeoDefaults::image((string)($page['social_image'] ?? ''), $baseUrl),
            'canonicalUrl' => $baseUrl . '/' . $canonicalSlug,
            'cmsPage' => $page,
            'breadcrumbs' => $breadcrumbs,
        ] + $extra;
        Response::html(View::render($fallbackView, $data));
    }


}
