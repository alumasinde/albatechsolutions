<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;

final class StaticPageController
{
    public function about(): void
    {
        $this->render('pages/about', 'About AlbaTech Solutions', 'Learn how AlbaTech Solutions helps people and businesses with practical web design, online presence and digital assistance in Kenya.', 'about');
    }

    public function getHelp(): void
    {
        $this->render('pages/get-help', 'Get Assistance', 'Tell AlbaTech Solutions what you need help with and explore available services.', 'get-help');
    }

    public function contactDetails(): void
    {
        $this->render('pages/contact-details', 'Contact & Location', 'Find AlbaTech Solutions contact information and service-area details.', 'contact-details');
    }

    public function terms(): void
    {
        $this->render('pages/terms', 'Terms of Service', 'Read the terms that apply when using AlbaTech Solutions services and website.', 'terms');
    }

    public function privacy(): void
    {
        $this->render('pages/privacy', 'Privacy Policy', 'Learn how AlbaTech Solutions handles enquiries and personal information.', 'privacy');
    }

    private function render(string $view, string $title, string $description, string $slug): void
    {
        $baseUrl = SiteUrl::base();
        Response::html(View::render($view, [
            'pageTitle' => $title . ' | AlbaTech Solutions',
            'metaDescription' => $description,
            'canonicalUrl' => $baseUrl . '/' . $slug,
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
        ]));
    }
}
