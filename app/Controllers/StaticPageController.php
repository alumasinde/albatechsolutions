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
        $this->render('pages/get-help', 'Get Assistance', 'Tell AlbaTech Solutions what you need help with and explore available services.');
    }

    private function render(string $view, string $title, string $description): void
    {
        $baseUrl = rtrim(Env::get('APP_URL', 'http://albatechsolution.test:8000') ?? '', '/');
        Response::html(View::render($view, [
            'pageTitle' => $title . ' | AlbaTech Solutions',
            'metaDescription' => $description,
            'canonicalUrl' => $baseUrl . '/' . match ($view) {
                'pages/about' => 'about',
                'pages/guides' => 'guides',
                default => 'get-help',
            },
        ]));
    }
}
