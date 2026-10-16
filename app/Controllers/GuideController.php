<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Database;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\SeoDefaults;

final class GuideController
{
    private const META_COLUMNS = 'title, slug, category, summary, meta_title, meta_description, published_at, updated_at';

    /** @return array<string, array<string, mixed>> Listing data for every published guide, keyed by slug (no article bodies). */
    public static function articlesMeta(): array
    {
        return self::load(self::META_COLUMNS, null);
    }

    /** @return array<string, mixed>|null A single published guide with its full body. */
    public static function article(string $slug): ?array
    {
        $articles = self::load(self::META_COLUMNS . ', intro, content_html', $slug);
        return $articles[$slug] ?? null;
    }

    /** @return array<string, array<string, mixed>> */
    private static function load(string $columns, ?string $slug): array
    {
        try {
            $sql = "SELECT {$columns} FROM guides WHERE status = 'published'"
                . ($slug !== null ? ' AND slug = :slug' : '')
                . ' ORDER BY published_at DESC, updated_at DESC';
            $statement = Database::connection()->prepare($sql);
            $statement->execute($slug !== null ? ['slug' => $slug] : []);
            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $articles = [];
            foreach ($rows as $row) {
                $articleSlug = (string) ($row['slug'] ?? '');
                if ($articleSlug === '') {
                    continue;
                }

                $publishedAt = !empty($row['published_at']) ? strtotime((string) $row['published_at']) : false;
                $updatedAt = !empty($row['updated_at']) ? strtotime((string) $row['updated_at']) : false;
                $articles[$articleSlug] = [
                    'title' => (string) $row['title'],
                    'meta_title' => (string) (($row['meta_title'] ?? '') ?: $row['title']),
                    'meta_description' => (string) (($row['meta_description'] ?? '') ?: $row['summary']),
                    'summary' => (string) $row['summary'],
                    'category' => (string) $row['category'],
                    'published' => $publishedAt !== false ? date('Y-m-d', $publishedAt) : '',
                    'updated' => $updatedAt !== false ? date('Y-m-d', $updatedAt) : '',
                    'intro' => (string) ($row['intro'] ?? ''),
                    // Content is sanitised by GuideAdminController on save and by the seed review process.
                    'content_html' => (string) ($row['content_html'] ?? ''),
                    'sections' => [],
                ];
            }

            return $articles;
        } catch (\Throwable $exception) {
            error_log('[AlbaTech guides] Could not load guides from database: ' . $exception->getMessage());
            return [];
        }
    }

    public function index(): void
    {
        $articles = self::articlesMeta();
        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guides', [
            'pageTitle' => SeoDefaults::title('', 'Practical Business & Website Guides for Kenya | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description('', 'Practical guides for Kenyan small businesses on website costs in Kenya, domains and hosting, preparing a business website, and improving local online visibility in Nairobi and across Kenya.'),
            'canonicalUrl' => $baseUrl . '/guides',
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'guideArticles' => $articles,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Guides', 'url' => '/guides'],
            ],
        ]));
    }

    public function show(\AlbaTech\Core\Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $article = self::article($slug);

        if ($article === null) {
            Response::html(View::render('errors/404', [
                'pageTitle' => 'Guide not found | AlbaTech Solutions',
                'metaRobots' => 'noindex, nofollow',
                'canonicalUrl' => '',
            ]), 404);
            return;
        }

        $baseUrl = SiteUrl::base();
        Response::html(View::render('pages/guide-article', [
            'pageTitle' => SeoDefaults::brandedTitle((string) $article['meta_title'], (string) $article['title']),
            'metaDescription' => SeoDefaults::description((string) $article['meta_description'], (string) $article['summary']),
            'canonicalUrl' => $baseUrl . '/guides/' . rawurlencode($slug),
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'guideArticle' => $article,
            'guideSlug' => $slug,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Guides', 'url' => '/guides'],
                ['name' => $article['title'], 'url' => ''],
            ],
        ]));
    }
}
