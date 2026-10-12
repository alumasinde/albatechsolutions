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
            'navigationItems' => $this->navigation('header'),
            'ctaItems' => $this->ctas(),
            'featuredServices' => $this->services->featuredServices(6),
            'featuredProjects' => $featuredProjects,
            'whatsAppUrl' => $whatsAppUrl,
            'hasWhatsApp' => $hasWhatsApp,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
            ],
        ]));
    }

    private function navigation(string $placement): array
    {
        try { $stmt = \AlbaTech\Core\Database::connection()->prepare('SELECT label,url,open_new_tab FROM site_navigation WHERE placement=:placement AND is_enabled=1 ORDER BY sort_order,id'); $stmt->execute(['placement'=>$placement]); return $stmt->fetchAll(); } catch (\Throwable $e) { return []; }
    }

    private function ctas(): array
    {
        try { $rows = \AlbaTech\Core\Database::connection()->query('SELECT cta_key,label,url,placement,style_class FROM site_ctas WHERE is_enabled=1')->fetchAll(); $out=[]; foreach ($rows as $row) $out[$row['cta_key']]=$row; return $out; } catch (\Throwable $e) { return []; }
    }
}
