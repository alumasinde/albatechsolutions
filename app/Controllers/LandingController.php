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
use Throwable;

/** Hand-written landing pages for the two homepage doors. Routed before /services/{slug}. */
final class LandingController
{
    public function complianceHub(Request $request): void
    {
        $settings = (new SiteSettingsRepository())->publicValues();
        $services = [];
        try {
            $services = (new ServiceRepository())->complianceServices(12);
        } catch (Throwable $e) {
            error_log('[AlbaTech Landing] Compliance services unavailable: ' . $e->getMessage());
        }
        $base = SiteUrl::base();

        Response::html(View::render('landing/compliance-hub', [
            'pageTitle' => 'KRA, eTIMS & eCitizen Help in Nairobi | AlbaTech',
            'metaDescription' => 'Fast help with KRA returns, eTIMS, business registration, BRS annual returns, eCitizen, SHA and NTSA. Price confirmed first, documents kept confidential.',
            'canonicalUrl' => $base . '/services/kra-ecitizen-help',
            'metaImage' => $base . '/assets/images/albatech-social-card.jpg',
            'schemaPageType' => 'CollectionPage',
            'services' => $services,
            'whatsAppUrl' => $this->whatsApp($settings, 'Hi AlbaTech, I need help with a KRA / eCitizen task. Here is what I need:'),
            'faqItems' => [
                ['question' => 'Is AlbaTech Solutions a government agency?', 'answer' => 'No. We are an independent service provider. Government fees are paid to the relevant authority, and decisions, requirements and processing times remain with that authority.'],
                ['question' => 'How do you handle my KRA or eCitizen login details?', 'answer' => 'We use your details only for the task you asked for, never share them, and delete any copies of your documents when the work is done. We recommend changing your password after the task.'],
                ['question' => 'How is the price confirmed?', 'answer' => 'Tell us the task on WhatsApp or through the form. We confirm the price and what is included before starting, so there are no surprise charges.'],
                ['question' => 'Can I do it myself instead?', 'answer' => 'Yes. Our free guides walk through the common tasks step by step. If you get stuck, send us a message and we can pick it up from there.'],
            ],
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Services', 'url' => '/services'],
                ['name' => 'Compliance help', 'url' => ''],
            ],
        ]));
    }

    public function smeLaunchpad(Request $request): void
    {
        $settings = (new SiteSettingsRepository())->publicValues();
        $base = SiteUrl::base();

        Response::html(View::render('landing/sme-launchpad', [
            'pageTitle' => 'SME Launchpad: Register & Get Online in Kenya | AlbaTech',
            'metaDescription' => 'Business registration, KRA PIN and eTIMS, a .co.ke domain, a business website and a Google Business Profile, handled together for Kenyan SMEs.',
            'canonicalUrl' => $base . '/services/sme-launchpad',
            'metaImage' => $base . '/assets/images/albatech-social-card.jpg',
            'schemaService' => [
                'name' => 'SME Launchpad',
                'short_description' => 'Business registration, KRA and eTIMS setup, .co.ke domain, business website and Google Business Profile for new Kenyan businesses.',
                'pricing_type' => 'quote',
            ],
            'whatsAppUrl' => $this->whatsApp($settings, 'Hi AlbaTech, I am interested in the SME Launchpad. My business is:'),
            'faqItems' => [
                ['question' => 'Do I have to take the whole package?', 'answer' => 'No. Pick the steps you still need. If your business is already registered, we start from the domain and website.'],
                ['question' => 'How much does it cost?', 'answer' => 'It depends on which steps you need and the size of the website. We confirm a fixed price in writing after a short call. Government fees, such as BRS registration, are paid to the authority and listed separately.'],
                ['question' => 'How long does it take?', 'answer' => 'The website and Google Business Profile follow a timeline we agree before starting. Business registration depends on the registry, so we tell you the expected time and keep you updated.'],
                ['question' => 'Who owns the domain and website?', 'answer' => 'You do. The domain is registered in your business name and you get the login details at handover.'],
            ],
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Services', 'url' => '/services'],
                ['name' => 'SME Launchpad', 'url' => ''],
            ],
        ]));
    }

    private function whatsApp(array $settings, string $message): string
    {
        return WhatsAppLink::build(array_merge($settings, ['whatsapp_prefill_message' => $message]));
    }
}
