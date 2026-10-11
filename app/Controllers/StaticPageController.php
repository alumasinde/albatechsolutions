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
    public function about(): void { $this->render('about', 'pages/about', 'About AlbaTech Solutions', 'Learn how AlbaTech Solutions helps people and businesses with practical web design, online presence and digital assistance in Kenya.', 'about'); }
    public function getHelp(): void { $this->render('get-help', 'pages/get-help', 'Get Assistance', 'Tell AlbaTech Solutions what you need help with and explore available services.', 'get-help', ['csrf' => AdminSecurity::csrfToken()]); }
    public function contactDetails(): void { $this->render('contact-details', 'pages/contact-details', 'Contact & Location', 'Find AlbaTech Solutions contact information and service-area details.', 'contact-details'); }
    public function terms(): void { $this->render('terms', 'pages/terms', 'Terms of Service', 'Read the terms that apply when using AlbaTech Solutions services and website.', 'terms'); }
    public function privacy(): void { $this->render('privacy', 'pages/privacy', 'Privacy Policy', 'Learn how AlbaTech Solutions handles enquiries and personal information.', 'privacy'); }

    private function render(string $slug, string $fallbackView, string $title, string $description, string $canonicalSlug, array $extra = []): void
    {
        $baseUrl = SiteUrl::base();
        $page = null;
        try { $stmt = Database::connection()->prepare('SELECT * FROM cms_pages WHERE slug=:slug LIMIT 1'); $stmt->execute(['slug' => $slug]); $page = $stmt->fetch(); } catch (Throwable $e) { error_log('[AlbaTech CMS page] ' . $e->getMessage()); }
        if (is_array($page) && ($page['status'] ?? '') !== 'published') {
            Response::html(View::render('errors/404', ['pageTitle' => 'Page not found | AlbaTech Solutions', 'metaRobots' => 'noindex, nofollow', 'canonicalUrl' => '']), 404);
            return;
        }
        if ($page && $slug !== 'get-help') {
            Response::html(View::render('pages/cms-static', [
                'pageTitle' => SeoDefaults::title((string)($page['meta_title'] ?? ''), (string)($page['title'] ?? $title) . ' | AlbaTech Solutions'),
                'metaDescription' => SeoDefaults::description((string)($page['meta_description'] ?? ''), (string)($page['intro'] ?? '') ?: $description),
                'metaImage' => SeoDefaults::image((string)($page['social_image'] ?? ''), $baseUrl),
                'canonicalUrl' => $baseUrl . '/' . $canonicalSlug,
                'cmsPage' => $page,
            ]));
            return;
        }
        $data = [
            'pageTitle' => SeoDefaults::title((string)($page['meta_title'] ?? ''), $title . ' | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description((string)($page['meta_description'] ?? ''), (string)($page['intro'] ?? '') ?: $description),
            'metaImage' => SeoDefaults::image((string)($page['social_image'] ?? ''), $baseUrl),
            'canonicalUrl' => $baseUrl . '/' . $canonicalSlug,
            'cmsPage' => $page,
        ] + $extra;
        Response::html(View::render($fallbackView, $data));
    }


}
