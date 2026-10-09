<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;

final class StaticPageController
{
    public function about(): void
    {
        $this->render('pages/about', 'About AlbaTech Solutions', 'Learn how AlbaTech Solutions helps people and businesses with practical web design, online presence and digital assistance in Kenya.');
    }

    public function guides(): void
    {
        $this->render('pages/guides', 'Guides & Resources', 'Practical guides for websites, online business presence and selected digital processes in Kenya.');
    }

    public function getHelp(): void
    {
        $this->render('pages/get-help', 'Contact AlbaTech Solutions', 'Contact AlbaTech Solutions for practical web design, IT support and digital assistance in Kenya.');
    }

    public function contactDetails(): void
    {
        $this->render('pages/contact-details', 'Contact & Location', 'Find AlbaTech Solutions contact information and service location details.');
    }

    public function terms(): void
    {
        $this->render('pages/terms', 'Terms of Service', 'Read the terms that apply when using AlbaTech Solutions services and website.');
    }

    public function privacy(): void
    {
        $this->render('pages/privacy', 'Privacy Policy', 'Learn how AlbaTech Solutions handles enquiries and personal information.');
    }

    private function render(string $view, string $title, string $description): void
    {
        $baseUrl = rtrim(Env::get('APP_URL', 'http://albatechsolution.test:8000') ?? '', '/');
        Response::html(View::render($view, [
            'pageTitle' => $title . ' | AlbaTech Solutions',
            'metaDescription' => $description,
            'canonicalUrl' => $baseUrl . match ($view) {
                'pages/about' => '/about',
                'pages/guides' => '/guides',
                'pages/get-help' => '/get-help',
                'pages/contact-details' => '/contact-details',
                'pages/terms' => '/terms',
                'pages/privacy' => '/privacy',
                default => '/',
            },
        ]));
    }
}
