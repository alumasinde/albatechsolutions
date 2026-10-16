<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\SeoDefaults;
use AlbaTech\Core\Request;
use AlbaTech\Core\View;
use AlbaTech\Repositories\ServiceRepository;
use Throwable;

final class ServiceController
{
    public function __construct(private readonly ServiceRepository $services = new ServiceRepository())
    {
    }

    public function index(): void
    {
        try {
            $categories = $this->services->categoriesWithServices();
        } catch (Throwable $exception) {
            throw $exception;
        }

        $baseUrl = SiteUrl::base();
        Response::html(View::render('services/index', [
            'pageTitle' => SeoDefaults::title('', 'Website Design, Business Registration & Digital Services in Kenya | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description('', 'Professional website design, business registration, KRA returns filing, and digital assistance services in Kenya. Clear pricing and practical solutions.'),
            'canonicalUrl' => $baseUrl . '/services',
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'categories' => $categories,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Services', 'url' => '/services'],
            ],
        ]));
    }

    public function show(Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $service = $this->services->findPublishedBySlug($slug);

        if ($service === null) {
            Response::html(View::render('errors/404', [
                'pageTitle' => 'Service not found | AlbaTech Solutions',
                'metaRobots' => 'noindex, nofollow',
                'canonicalUrl' => '',
            ]), 404);
            return;
        }

        $baseUrl = SiteUrl::base();
        $description = trim((string) ($service['meta_description'] ?? ''));
        if ($description === '') {
            $description = trim((string) ($service['short_description'] ?? 'Explore this service from AlbaTech Solutions.'));
        }

        Response::html(View::render('services/show', [
            'pageTitle' => SeoDefaults::brandedTitle((string) ($service['meta_title'] ?? ''), (string) $service['name']),
            'metaDescription' => SeoDefaults::description($description, (string) ($service['short_description'] ?? 'Explore this service from AlbaTech Solutions.')),
            'canonicalUrl' => $baseUrl . '/services/' . rawurlencode($service['slug']),
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'metaRobots' => 'index, follow, max-image-preview:large',
            'service' => $service,
            'schemaService' => $service,
            'relatedProjects' => (new \AlbaTech\Repositories\WorkRepository())->relatedToCategory((int)$service['category_id'], 0, 3),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Services', 'url' => '/services'],
                ['name' => $service['name'], 'url' => ''],
            ],
        ]));
    }
}
