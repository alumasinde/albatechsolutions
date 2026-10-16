<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Core\WhatsAppLink;
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
        $whatsAppUrl = WhatsAppLink::build($settings);
        $hasWhatsApp = str_starts_with($whatsAppUrl, 'https://wa.me/');

        $featuredProjects = [];
        try {
            $featuredProjects = (new \AlbaTech\Repositories\WorkRepository())->featured(8);
        } catch (\Throwable $e) { error_log('[AlbaTech Work] Featured projects unavailable: ' . $e->getMessage()); }

        Response::html(View::render('pages/home', [
            'pageTitle' => SeoDefaults::title((string) ($settings['home_meta_title'] ?? ''), 'Website Design & Business Registration in Nairobi | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description((string) ($settings['home_meta_description'] ?? ''), (string) ($settings['default_meta_description'] ?? SeoDefaults::DESCRIPTION)),
            'metaImage' => SeoDefaults::image((string) ($settings['home_social_image'] ?? $settings['default_social_image'] ?? $settings['home_hero_image'] ?? ''), $baseUrl),
            'canonicalUrl' => $baseUrl . '/',
            'appName' => $appName,
            'homeContent' => $settings,
            'ctaItems' => (new \AlbaTech\Repositories\CmsRepository())->ctas(),
            'featuredServices' => $this->services->featuredServices(6),
            'featuredProjects' => $featuredProjects,
            'whatsAppUrl' => $whatsAppUrl,
            'hasWhatsApp' => $hasWhatsApp,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
            ],
        ]));
    }
}
