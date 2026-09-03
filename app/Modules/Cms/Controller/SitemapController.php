<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Cms\Repository\BlogPostRepository;

final class SitemapController
{
    private string $baseUrl;

    public function __construct(
        private readonly ServiceRepository $services,
        private readonly BlogPostRepository $posts
    ) {
        $this->baseUrl = rtrim((string) \App\Core\Config::get('app.url', 'https://albatechsolutions.co.ke'), '/');
    }


    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = [];

        /**
         * Important static pages
         */
        $this->addUrl(
            $urls,
            '/',
            'weekly',
            '1.0'
        );

        $this->addUrl(
            $urls,
            '/services',
            'weekly',
            '0.9'
        );

        $this->addUrl(
            $urls,
            '/blog',
            'weekly',
            '0.8'
        );

        $this->addUrl(
            $urls,
            '/faqs',
            'monthly',
            '0.7'
        );

        $this->addUrl(
            $urls,
            '/contact',
            'monthly',
            '0.7'
        );

        $this->addUrl(
            $urls,
            '/about',
            'monthly',
            '0.6'
        );

        $this->addUrl(
            $urls,
            '/privacy-policy',
            'yearly',
            '0.4'
        );


        /**
         * Service pages
         */
        foreach ($this->services->allPublished() as $service) {

            $this->addUrl(
                $urls,
                '/services/' . $service['slug'],
                'monthly',
                '0.8',
                $service['updated_at'] ?? null
            );
        }


        /**
         * Blog posts
         */
        foreach ($this->posts->allPublishedForSitemap() as $post) {

            $this->addUrl(
                $urls,
                '/blog/' . $post['slug'],
                'monthly',
                '0.7',
                $post['updated_at'] ?? null
            );
        }


        /**
         * Blog category pages
         */
        $this->addUrl(
            $urls,
            '/blog?category=company-news',
            'weekly',
            '0.7'
        );

        $this->addUrl(
            $urls,
            '/blog?category=guides',
            'weekly',
            '0.8'
        );


        echo '<?xml version="1.0" encoding="UTF-8"?>';

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {

            echo '<url>';

            echo '<loc>'
                . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8')
                . '</loc>';

            if (!empty($url['lastmod'])) {
                echo '<lastmod>'
                    . $url['lastmod']
                    . '</lastmod>';
            }

            echo '<changefreq>'
                . $url['changefreq']
                . '</changefreq>';

            echo '<priority>'
                . $url['priority']
                . '</priority>';

            echo '</url>';
        }

        echo '</urlset>';

        exit;
    }


    private function addUrl(
        array &$urls,
        string $path,
        string $changefreq,
        string $priority,
        ?string $lastmod = null
    ): void {
        $urls[] = [
            'loc' => $this->baseUrl . $path,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'lastmod' => $lastmod
                ? date('Y-m-d', strtotime($lastmod))
                : date('Y-m-d')
        ];
    }
}