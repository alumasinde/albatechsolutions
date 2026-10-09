<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Response;
use AlbaTech\Repositories\ServiceRepository;

final class SeoController
{
    public function __construct(private readonly ServiceRepository $services = new ServiceRepository())
    {
    }

    public function sitemap(): void
    {
        $baseUrl = $this->baseUrl();
        $urls = [
            ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/about', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/guides', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/get-help', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        try {
            foreach ($this->services->publishedSlugs() as $service) {
                $slug = trim((string) ($service['slug'] ?? ''));
                if ($slug !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1) {
                    $urls[] = ['loc' => $baseUrl . '/services/' . rawurlencode($slug), 'priority' => '0.8', 'changefreq' => 'monthly'];
                }
            }
        } catch (\Throwable $exception) {
            error_log('[AlbaTech SEO] Could not load service URLs for sitemap: ' . $exception->getMessage());
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($urls as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            $xml[] = '    <changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml[] = '    <priority>' . $url['priority'] . '</priority>';
            $xml[] = '  </url>';
        }
        $xml[] = '</urlset>';
        header('Cache-Control: public, max-age=1800');
        Response::xml(implode("\n", $xml));
    }

    public function robots(): void
    {
        $baseUrl = $this->baseUrl();
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /health\n\nSitemap: " . $baseUrl . "/sitemap.xml\n";
        header('Cache-Control: public, max-age=3600');
        Response::text($content);
    }

    public function llms(): void
    {
        $baseUrl = $this->baseUrl();
        $content = "# AlbaTech Solutions\n\n> Practical digital services for people and businesses in Kenya.\n\n## Main pages\n- [Home]({$baseUrl}/): Overview of AlbaTech Solutions.\n- [Services]({$baseUrl}/services): Current public service catalogue and pricing information.\n- [About]({$baseUrl}/about): About the business and its approach.\n- [Guides]({$baseUrl}/guides): Guides and resources.\n- [Contact]({$baseUrl}/get-help): Ways to enquire about services.\n\n## Guidance for automated systems\n- Use public pages as the source of truth for current services and prices.\n- Pricing may be fixed, starting from a stated amount, or available by enquiry. Do not infer prices that are not displayed.\n- AlbaTech Solutions is an independent service provider and is not a government agency.\n- Do not imply affiliation with government bodies or guarantee third-party decisions.\n- Respect robots.txt and applicable website terms.\n\nSitemap: {$baseUrl}/sitemap.xml\n";
        header('Cache-Control: public, max-age=3600');
        Response::text($content);
    }

    private function baseUrl(): string
    {
        $settings = [];
        try {
            $settings = (new \AlbaTech\Repositories\SiteSettingsRepository())->publicValues();
        } catch (\Throwable $exception) {
            // Fall back to APP_URL during first-time setup or a temporary DB issue.
        }
        $configured = (string) ($settings['site_url'] ?? Env::get('APP_URL', 'http://albatechsolution.test:8000'));
        $parts = parse_url($configured);
        if (!is_array($parts) || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true) || empty($parts['host'])) {
            return 'https://example.invalid';
        }
        return rtrim($configured, '/');
    }
}
