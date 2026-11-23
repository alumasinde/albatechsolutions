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
            'pageTitle' => SeoDefaults::title('', 'Software, Web & Business Services | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description('', 'Custom booking, property, visitor and inventory systems, business websites and independent assistance with business registration and KRA filing in Kenya.'),
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

        $pricingType = (string) ($service['pricing_type'] ?? '');
        $pricingAmount = $service['pricing_amount'] ?? null;
        $pricingAnswer = 'Pricing depends on your requirements. We review what you need and confirm the scope and price in writing before you commit.';
        if (in_array($pricingType, ['fixed', 'starting_at'], true) && $pricingAmount !== null && $pricingAmount !== '' && is_numeric($pricingAmount)) {
            $amount = number_format((float) $pricingAmount, ((float) $pricingAmount == (int) $pricingAmount) ? 0 : 2);
            $pricingAnswer = ($pricingType === 'starting_at' ? 'The service starts from KSh ' : 'The price is KSh ') . $amount . '. The final cost depends on scope and options, and we confirm it in writing before work begins.';
        }

        $isSoftwareService = ($service['category_slug'] ?? '') === 'software-business-systems';
        $faqItems = [
            ['question' => 'How is pricing confirmed?', 'answer' => $pricingAnswer],
            ['question' => 'How long does it take?', 'answer' => trim((string) ($service['turnaround'] ?? '')) !== ''
                ? 'Typical turnaround is ' . lcfirst(rtrim(trim((string) $service['turnaround']), '.')) . '. ' . ($isSoftwareService ? 'We agree milestones before development starts' : 'For government processes the final step depends on the relevant authority') . ', and we tell you if anything changes.'
                : ($isSoftwareService ? 'A small system can take a few weeks; larger workflows take longer. We agree milestones and an expected timeline before development starts, and tell you if anything changes.' : 'Turnaround depends on the specific requirement and, for government processes, on the relevant authority. We confirm an expected timeline before starting and tell you if anything changes.')],
            ['question' => 'Do I need to visit an office?', 'answer' => 'Most work is handled remotely by WhatsApp, phone and email. If an official step requires you to appear in person, such as biometrics or document collection, we tell you before you start.'],
            $isSoftwareService
                ? ['question' => 'What happens after launch?', 'answer' => 'Support and handover are agreed before work starts. You will know what is included, what your team will manage and how changes are handled.']
                : ['question' => 'Is AlbaTech Solutions a government agency?', 'answer' => 'No. AlbaTech Solutions is an independent service provider. Government fees are paid to the relevant authority, and decisions, requirements and processing times remain with that authority.'],
        ];

        Response::html(View::render('services/show', [
            'pageTitle' => SeoDefaults::brandedTitle((string) ($service['meta_title'] ?? ''), (string) $service['name']),
            'metaDescription' => SeoDefaults::description($description, (string) ($service['short_description'] ?? 'Explore this service from AlbaTech Solutions.')),
            'canonicalUrl' => $baseUrl . '/services/' . rawurlencode($service['slug']),
            'metaImage' => SeoDefaults::image('', $baseUrl),
            'metaRobots' => 'index, follow, max-image-preview:large',
            'service' => $service,
            'schemaService' => $service,
            'faqItems' => $faqItems,
            'relatedProjects' => (new \AlbaTech\Repositories\WorkRepository())->relatedToCategory((int)$service['category_id'], 0, 3),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Services', 'url' => '/services'],
                ['name' => $service['name'], 'url' => ''],
            ],
        ]));
    }
}
