<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\SeoDefaults;
use Throwable;

final class StaticPageController
{
    public function about(): void { $this->render('about', 'pages/about', 'About AlbaTech Solutions | Digital Services in Nairobi, Kenya', 'Learn how AlbaTech Solutions helps businesses and individuals across Kenya with practical website design, business registration support and online visibility.', 'about', [], ['name' => 'About', 'url' => '/about']); }
    public function getHelp(): void { $this->render('get-help', 'pages/get-help', 'Get Help with a Website or Business Service in Kenya', 'Send an enquiry or start a WhatsApp chat about website design, business registration, KRA returns and other digital services in Kenya.', 'get-help', ['csrf' => AdminSecurity::csrfToken(), 'enquiryServices' => (new \AlbaTech\Repositories\ServiceRepository())->enquiryOptions()], ['name' => 'Contact', 'url' => '/get-help']); }
    public function contactDetails(): void { $this->render('contact-details', 'pages/contact-details', 'Contact & Location | AlbaTech Solutions Kenya', 'WhatsApp, phone and email details for AlbaTech Solutions in Nairobi, with nationwide remote support across Kenya.', 'contact-details', [], ['name' => 'Contact Details', 'url' => '/contact-details']); }
    public function terms(): void { $this->render('terms', 'pages/terms', 'Terms of Service', 'Read the terms that apply when using AlbaTech Solutions services and website.', 'terms', [], ['name' => 'Terms', 'url' => '/terms']); }
    public function privacy(): void { $this->render('privacy', 'pages/privacy', 'Privacy Policy', 'Learn how AlbaTech Solutions handles enquiries and personal information.', 'privacy', [], ['name' => 'Privacy', 'url' => '/privacy']); }

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
        if ($page && $slug !== 'get-help') {
            Response::html(View::render('pages/cms-static', [
                'pageTitle' => SeoDefaults::title((string)($page['meta_title'] ?? ''), (string)($page['title'] ?? $title) . ' | AlbaTech Solutions'),
                'metaDescription' => SeoDefaults::description((string)($page['meta_description'] ?? ''), (string)($page['intro'] ?? '') ?: $description),
                'metaImage' => SeoDefaults::image((string)($page['social_image'] ?? ''), $baseUrl),
                'canonicalUrl' => $baseUrl . '/' . $canonicalSlug,
                'cmsPage' => $page,
                'breadcrumbs' => $breadcrumbs,
            ]));
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
