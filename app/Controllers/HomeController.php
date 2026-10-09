<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;
use AlbaTech\Repositories\ServiceRepository;
use AlbaTech\Repositories\SiteSettingsRepository;

final class HomeController
{
    public function __construct(private readonly ServiceRepository $services = new ServiceRepository(), private readonly SiteSettingsRepository $settings = new SiteSettingsRepository())
    {
    }

    public function index(Request $request): void
    {
        $settings = $this->settings->publicValues();
        $appName = $settings['site_name'] ?? (Env::get('APP_NAME', 'AlbaTech Solutions') ?? 'AlbaTech Solutions');
        $baseUrl = rtrim($settings['site_url'] ?? (Env::get('APP_URL', 'http://albatechsolution.test:8000') ?? ''), '/');
        $configuredNumber = $settings['whatsapp_number'] ?? (Env::get('WHATSAPP_NUMBER', '') ?? '');
        $whatsAppNumber = preg_replace('/\D+/', '', $configuredNumber);
        $whatsAppUrl = $whatsAppNumber !== ''
            ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode('Hi AlbaTech Solutions, I would like to discuss a service.')
            : '/get-help';

        Response::html(View::render('pages/home', [
            'pageTitle' => $appName . ' | Practical Digital Solutions in Kenya',
            'metaDescription' => 'Websites, IT support and practical digital services for people and businesses in Kenya. Explore services and get a clear next step from AlbaTech Solutions.',
            'canonicalUrl' => $baseUrl . '/',
            'appName' => $appName,
            'featuredServices' => $this->services->featuredServices(6),
            'whatsAppUrl' => $whatsAppUrl,
        ]));
    }
}
