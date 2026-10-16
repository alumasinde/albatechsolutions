<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Repositories\ServiceRepository;
use Throwable;

final class SeoController
{
    public function __construct(private readonly ServiceRepository $services = new ServiceRepository())
    {
    }

    public function sitemap(): void
    {
        $baseUrl = SiteUrl::base();
        $urls = [
            ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/work', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/guides', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        // Only include CMS pages that are published and have a real public route.
        // Arbitrary CMS slugs are not routable in this application and must not be indexed.
        $cmsRoutes = [
            'about' => '/about',
            'get-help' => '/get-help',
            'contact-details' => '/contact-details',
            'terms' => '/terms',
            'privacy' => '/privacy',
        ];
        $dynamicSitemapIncomplete = false;
        try {
            $cmsPages = \AlbaTech\Core\Database::connection()->query(
                "SELECT slug, updated_at FROM cms_pages WHERE status='published' ORDER BY slug"
            )->fetchAll();
            foreach ($cmsPages as $page) {
                $slug = trim((string) ($page['slug'] ?? ''));
                if (!isset($cmsRoutes[$slug])) {
                    continue;
                }
                $entry = ['loc' => $baseUrl . $cmsRoutes[$slug], 'priority' => in_array($slug, ['about'], true) ? '0.6' : (in_array($slug, ['terms', 'privacy'], true) ? '0.2' : '0.5'), 'changefreq' => in_array($slug, ['terms', 'privacy'], true) ? 'yearly' : 'monthly'];
                $updatedRaw = trim((string) ($page['updated_at'] ?? ''));
                $updated = $updatedRaw !== '' ? strtotime($updatedRaw) : false;
                if ($updated !== false) $entry['lastmod'] = date('Y-m-d', $updated);
                $urls[] = $entry;
            }
        } catch (Throwable $exception) {
            $dynamicSitemapIncomplete = true;
            error_log('[AlbaTech SEO] Could not load CMS pages for sitemap: ' . $exception->getMessage());
        }


        foreach (GuideController::articlesMeta() as $slug => $article) {
            $guideUrl = ['loc' => $baseUrl . '/guides/' . rawurlencode((string) $slug), 'priority' => '0.7', 'changefreq' => 'monthly'];
            $guideDate = (string) ($article['updated'] ?? '');
            if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $guideDate) === 1) {
                $guideUrl['lastmod'] = $guideDate;
            }
            $urls[] = $guideUrl;
        }

        try {
            foreach ($this->services->publishedSlugs() as $service) {
                $slug = trim((string) ($service['slug'] ?? ''));
                if ($slug !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1) {
                    $entry = ['loc' => $baseUrl . '/services/' . rawurlencode($slug), 'priority' => '0.8', 'changefreq' => 'monthly'];
                    $updatedRaw = trim((string) ($service['updated_at'] ?? ''));
                    $updated = $updatedRaw !== '' ? strtotime($updatedRaw) : false;
                    if ($updated !== false) {
                        $entry['lastmod'] = date('Y-m-d', $updated);
                    }
                    $urls[] = $entry;
                }
            }
        } catch (Throwable $exception) {
            $dynamicSitemapIncomplete = true;
            error_log('[AlbaTech SEO] Could not load service URLs for sitemap: ' . $exception->getMessage());
        }

        try {
            $projects = \AlbaTech\Core\Database::connection()->query("SELECT slug, updated_at FROM work_projects WHERE status='published' ORDER BY updated_at DESC")->fetchAll();
            foreach ($projects as $project) {
                $slug = trim((string)($project['slug'] ?? ''));
                if ($slug !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1) {
                    $entry = ['loc' => $baseUrl . '/work/' . rawurlencode($slug), 'priority' => '0.7', 'changefreq' => 'monthly'];
                    $updatedRaw = trim((string) ($project['updated_at'] ?? ''));
                    $updated = $updatedRaw !== '' ? strtotime($updatedRaw) : false;
                    if ($updated !== false) $entry['lastmod'] = date('Y-m-d', $updated);
                    $urls[] = $entry;
                }
            }
        } catch (Throwable $exception) {
            $dynamicSitemapIncomplete = true;
            error_log('[AlbaTech SEO] Could not load work URLs for sitemap: ' . $exception->getMessage());
        }

        // Do not serve/cache a sitemap that silently omits published dynamic pages.
        if ($dynamicSitemapIncomplete) {
            header('Cache-Control: no-store, max-age=0');
            Response::text('Sitemap temporarily unavailable. Please retry later.', 503);
            return;
        }

        // Avoid duplicate URLs and guarantee valid XML output.
        $unique = [];
        foreach ($urls as $url) {
            $unique[$url['loc']] = $url;
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($unique as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            if (!empty($url['lastmod'])) {
                $xml[] = '    <lastmod>' . htmlspecialchars((string) $url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</lastmod>';
            }
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
        $baseUrl = SiteUrl::base();
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /health\n\nSitemap: " . $baseUrl . "/sitemap.xml\n";
        header('Cache-Control: public, max-age=3600');
        Response::text($content);
    }

    public function llms(): void
    {
        $baseUrl = SiteUrl::base();
        $lines = [
            '# AlbaTech Solutions',
            '',
            '> Practical website, online presence and digital assistance for people and businesses in Kenya.',
            '',
            '## Main pages',
            '- [Home](' . $baseUrl . '/): Overview of AlbaTech Solutions.',
            '- [Services](' . $baseUrl . '/services): Public service catalogue and pricing information.',
            '- [Our Work](' . $baseUrl . '/work): Published portfolio projects and case studies.',
            '- [Guides](' . $baseUrl . '/guides): Practical articles and checklists.',
        ];
        $cmsRoutes = [
            'about' => ['path' => '/about', 'label' => 'About', 'summary' => 'About the business and its approach.'],
            'get-help' => ['path' => '/get-help', 'label' => 'Contact', 'summary' => 'Ways to enquire about services.'],
            'contact-details' => ['path' => '/contact-details', 'label' => 'Contact Details', 'summary' => 'Full contact information and location details.'],
            'privacy' => ['path' => '/privacy', 'label' => 'Privacy policy', 'summary' => 'How enquiry information is handled.'],
            'terms' => ['path' => '/terms', 'label' => 'Terms of service', 'summary' => 'Terms for using the website and services.'],
        ];
        try {
            $cmsPages = \AlbaTech\Core\Database::connection()->query("SELECT slug,title,intro FROM cms_pages WHERE status='published' ORDER BY title")->fetchAll();
            foreach ($cmsPages as $page) {
                $slug = (string) ($page['slug'] ?? '');
                if (!isset($cmsRoutes[$slug])) continue;
                $route = $cmsRoutes[$slug];
                $label = trim((string) ($page['title'] ?? '')) ?: $route['label'];
                $summary = trim((string) ($page['intro'] ?? '')) ?: $route['summary'];
                $lines[] = '- [' . str_replace([']', '[', "\r", "\n"], '', $label) . '](' . $baseUrl . $route['path'] . '): ' . str_replace(["\r", "\n"], ' ', $summary);
            }
        } catch (Throwable $exception) {
            error_log('[AlbaTech SEO] Could not load CMS pages for llms.txt: ' . $exception->getMessage());
        }
        $lines[] = '';
        $lines[] = '## Guides';
        foreach (GuideController::articlesMeta() as $slug => $article) {
            $lines[] = '- [' . $article['title'] . '](' . $baseUrl . '/guides/' . $slug . '): ' . $article['summary'];
        }
        $lines = array_merge($lines, [
            '',
            '## Guidance for automated systems',
            '- Use public pages as the source of truth for current services and prices.',
            '- Pricing may be fixed, starting from a stated amount, or available by enquiry. Do not infer prices that are not displayed.',
            '- AlbaTech Solutions is an independent service provider and is not a government agency.',
            '- Do not imply affiliation with government bodies or guarantee third-party decisions.',
            '- Respect robots.txt and applicable website terms.',
            '',
            'Sitemap: ' . $baseUrl . '/sitemap.xml',
            '',
        ]);
        header('Cache-Control: public, max-age=3600');
        Response::text(implode("\n", $lines));
    }
}
