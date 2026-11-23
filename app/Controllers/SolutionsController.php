<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\WhatsAppLink;
use AlbaTech\Repositories\ServiceRepository;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WorkRepository;

final class SolutionsController
{
    public function index(Request $request): void
    {
        $solutions = (new ServiceRepository())->solutions(8);
        $proof = (new WorkRepository())->featured(1);
        $settings = (new SiteSettingsRepository())->publicValues();
        $base = SiteUrl::base();

        Response::html(View::render('solutions/index', [
            'pageTitle' => 'Software Solutions for Kenyan Businesses | AlbaTech Solutions',
            'metaDescription' => 'Booking, property, visitor, stock and custom internal systems built for Kenyan businesses. See what AlbaTech can build and request a quote.',
            'canonicalUrl' => $base . '/solutions',
            'metaImage' => $base . '/assets/images/albatech-social-card.jpg',
            'schemaPageType' => 'CollectionPage',
            'solutions' => $solutions,
            'proofProject' => $proof[0] ?? null,
            'whatsAppUrl' => WhatsAppLink::build($settings),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Solutions', 'url' => '/solutions'],
            ],
        ]));
    }
}
