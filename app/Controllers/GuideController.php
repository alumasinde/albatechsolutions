<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\SeoDefaults;

final class GuideController
{
    /** @return array<string, array<string, mixed>> */
    public static function articles(): array
    {
        try {
            $rows = \AlbaTech\Core\Database::connection()->query(
                "SELECT title, slug, category, summary, intro, content_html, meta_title,
                        meta_description, published_at, updated_at
                 FROM guides
                 WHERE status = 'published'
                 ORDER BY published_at DESC, updated_at DESC"
            )->fetchAll(\PDO::FETCH_ASSOC);

            $articles = [];
            foreach ($rows as $row) {
                $slug = (string) ($row['slug'] ?? '');
                if ($slug === '') {
                    continue;
                }

                $publishedAt = !empty($row['published_at']) ? strtotime((string) $row['published_at']) : false;
                $updatedAt = !empty($row['updated_at']) ? strtotime((string) $row['updated_at']) : false;
                $articles[$slug] = [
                    'title' => (string) $row['title'],
                    'meta_title' => (string) (($row['meta_title'] ?? '') ?: $row['title']),
                    'meta_description' => (string) (($row['meta_description'] ?? '') ?: $row['summary']),
                    'summary' => (string) $row['summary'],
                    'category' => (string) $row['category'],
                    'published' => $publishedAt !== false ? date('Y-m-d', $publishedAt) : '',
                    'updated' => $updatedAt !== false ? date('Y-m-d', $updatedAt) : '',
                    'intro' => (string) $row['intro'],
                    // Content is sanitised by GuideAdminController on save and by the seed review process.
                    'content_html' => (string) $row['content_html'],
                    'sections' => [],
                ];
            }

            return $articles;
        } catch (\Throwable $exception) {
            error_log('[AlbaTech guides] Could not load published guides from database: ' . $exception->getMessage());
            return [];
        }
    }

    public function index(): void
    {
        $articles = self::articles();
        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guides', [
            'pageTitle' => SeoDefaults::title('', 'Practical Business & Website Guides in Kenya | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description('', 'Practical guides for Kenyan small businesses on website costs, domains and hosting, preparing a business website, and improving local online visibility.'),
            'canonicalUrl' => $baseUrl . '/guides',
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'guideArticles' => $articles,
        ]));
    }

    public function show(\AlbaTech\Core\Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $articles = self::articles();
        $article = $articles[$slug] ?? null;

        if (!is_array($article)) {
            Response::html(View::render('errors/404', [
                'pageTitle' => 'Guide not found | AlbaTech Solutions',
                'metaRobots' => 'noindex, nofollow',
                'canonicalUrl' => '',
            ]), 404);
            return;
        }

        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guide-article', [
            'pageTitle' => SeoDefaults::title((string) $article['meta_title'], (string) $article['title']) . ' | AlbaTech Solutions',
            'metaDescription' => SeoDefaults::description((string) $article['meta_description'], (string) $article['summary']),
            'canonicalUrl' => $baseUrl . '/guides/' . rawurlencode($slug),
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'guideArticle' => $article,
            'guideSlug' => $slug,
        ]));
    }
}
