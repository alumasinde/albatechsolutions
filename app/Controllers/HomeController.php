<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\SiteUrl;
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
        $baseUrl = SiteUrl::base();
        $configuredNumber = (string) ($settings['whatsapp_number'] ?? '');
        $whatsAppNumber = preg_replace('/\D+/', '', $configuredNumber);
        $hasWhatsApp = strlen($whatsAppNumber) >= 8 && strlen($whatsAppNumber) <= 15 && !str_starts_with($whatsAppNumber, '0');
        $whatsAppUrl = $hasWhatsApp
            ? 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode('Hi AlbaTech Solutions, I would like to discuss a service.')
            : '/get-help';

        Response::html(View::render('pages/home', [
            'pageTitle' => $appName . ' | Practical Digital Solutions in Kenya',
            'metaDescription' => 'Website design, online business presence, business systems and practical digital assistance for people and businesses in Kenya. Explore services and get a clear next step.',
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
            'canonicalUrl' => $baseUrl . '/',
            'appName' => $appName,
            'featuredServices' => $this->services->featuredServices(6),
            'whatsAppUrl' => $whatsAppUrl,
            'hasWhatsApp' => $hasWhatsApp,
        ]));
    }
}
