<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;

final class HomeController
{
    public function index(Request $request): void
    {
        $appName = Env::get('APP_NAME', 'AlbaTech Solutions') ?? 'AlbaTech Solutions';
        $baseUrl = rtrim(Env::get('APP_URL', 'http://albatechsolution.dev:8000') ?? '', '/');

        Response::html(View::render('pages/home', [
            'pageTitle' => $appName . ' | Web Design & Digital Assistance in Kenya',
            'metaDescription' => 'Practical website design and digital assistance for individuals and businesses in Kenya. Tell us the task and we will help with the next step.',
            'canonicalUrl' => $baseUrl . '/',
            'appName' => $appName,
            'founderName' => 'Albert Masinde',
        ]));
    }
}
