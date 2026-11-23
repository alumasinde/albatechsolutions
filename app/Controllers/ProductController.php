<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Database;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\WhatsAppLink;
use AlbaTech\Repositories\SiteSettingsRepository;
use PDO;

final class ProductController
{
    public function index(Request $request): void
    {
        $products = Database::connection()->query(
            "SELECT w.id,w.title,w.slug,w.description,w.cover_image,w.lifecycle_status,w.platform,w.download_url,w.live_url,c.name AS category_name
             FROM work_projects w JOIN service_categories c ON c.id=w.category_id
             WHERE w.status='published' AND w.kind='product'
             ORDER BY w.is_featured DESC,w.sort_order,w.updated_at DESC"
        )->fetchAll();
        $base = SiteUrl::base();

        Response::html(View::render('products/index', [
            'pageTitle' => 'Products | Software by AlbaTech Solutions',
            'metaDescription' => 'Software products built by AlbaTech Solutions in Nairobi, including PesaLens for M-Pesa expense tracking, Budget254 and a rental management platform in early access.',
            'canonicalUrl' => $base . '/products',
            'metaImage' => $base . '/assets/images/albatech-social-card.jpg',
            'schemaPageType' => 'CollectionPage',
            'products' => $products,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Products', 'url' => '/products'],
            ],
        ]));
    }

    public function show(Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $stmt = Database::connection()->prepare(
            "SELECT w.*, c.name AS category_name, c.slug AS category_slug
             FROM work_projects w JOIN service_categories c ON c.id=w.category_id
             WHERE w.slug=:slug AND w.status='published' AND w.kind='product' LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            Response::html(View::render('errors/404', [
                'pageTitle' => 'Product not found | AlbaTech Solutions',
                'metaRobots' => 'noindex, nofollow',
                'canonicalUrl' => '',
            ]), 404);
            return;
        }

        $product['features'] = $this->decodeList($product['features_json'] ?? null);
        $product['stack'] = $this->decodeList($product['stack_json'] ?? null);
        $product['gallery'] = $this->decodeList($product['gallery_json'] ?? null);

        $relatedStmt = Database::connection()->prepare(
            "SELECT w.id,w.title,w.slug,w.description,w.cover_image,w.lifecycle_status,w.platform,c.name AS category_name
             FROM work_projects w JOIN service_categories c ON c.id=w.category_id
             WHERE w.status='published' AND w.kind='product' AND w.id<>:id
             ORDER BY w.is_featured DESC,w.sort_order,w.updated_at DESC LIMIT 3"
        );
        $relatedStmt->execute(['id' => $product['id']]);

        $settings = (new SiteSettingsRepository())->publicValues();
        $whatsAppUrl = WhatsAppLink::build($settings, '/get-help?product=' . rawurlencode($product['slug']));
        $base = SiteUrl::base();

        Response::html(View::render('products/show', [
            'pageTitle' => SeoDefaults::title((string) ($product['meta_title'] ?: ''), (string) $product['title'] . ' | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description((string) ($product['meta_description'] ?: $product['description'])),
            'canonicalUrl' => $base . '/products/' . rawurlencode($product['slug']),
            'metaImage' => SeoDefaults::image((string) ($product['cover_image'] ?? ''), $base),
            'product' => $product,
            'relatedProducts' => $relatedStmt->fetchAll(),
            'whatsAppUrl' => $whatsAppUrl,
            'schemaProduct' => $product,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Products', 'url' => '/products'],
                ['name' => $product['title'], 'url' => ''],
            ],
        ]));
    }

    /** @return list<string> */
    private function decodeList(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        return array_values(array_filter(array_map(
            static fn ($item): string => trim((string) $item),
            $decoded
        ), static fn (string $item): bool => $item !== ''));
    }
}
