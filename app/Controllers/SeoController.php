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
            ['loc' => $baseUrl . '/about', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/guides', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/get-help', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/contact-details', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/terms', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => $baseUrl . '/privacy', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ];

        foreach (GuideController::articles() as $slug => $article) {
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
                    $updated = strtotime((string) ($service['updated_at'] ?? ''));
                    if ($updated !== false) {
                        $entry['lastmod'] = date('Y-m-d', $updated);
                    }
                    $urls[] = $entry;
                }
            }
        } catch (Throwable $exception) {
            error_log('[AlbaTech SEO] Could not load service URLs for sitemap: ' . $exception->getMessage());
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
            '- [About](' . $baseUrl . '/about): About the business and its approach.',
            '- [Guides](' . $baseUrl . '/guides): Practical articles and checklists.',
            '- [Contact](' . $baseUrl . '/get-help): Ways to enquire about services.',
            '- [Contact details](' . $baseUrl . '/contact-details): Public contact and service-area information.',
            '- [Privacy policy](' . $baseUrl . '/privacy): How enquiry information is handled.',
            '- [Terms of service](' . $baseUrl . '/terms): General terms for using the website and services.',
            '',
            '## Guides',
        ];
        foreach (GuideController::articles() as $slug => $article) {
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
