<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Env;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
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
            'pageTitle' => 'Services | AlbaTech Solutions',
            'metaDescription' => 'Explore web design, online business presence and selected government and business assistance services in Kenya.',
            'canonicalUrl' => $baseUrl . '/services',
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
            'categories' => $categories,
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
            'pageTitle' => ($service['meta_title'] ?: $service['name']) . ' | AlbaTech Solutions',
            'metaDescription' => $description,
            'canonicalUrl' => $baseUrl . '/services/' . rawurlencode($service['slug']),
            'metaImage' => $baseUrl . '/assets/images/albatech-social-card.png',
            'metaRobots' => 'index, follow, max-image-preview:large',
            'service' => $service,
            'schemaService' => $service,
        ]));
    }
}
