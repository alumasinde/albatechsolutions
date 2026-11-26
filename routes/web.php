<?php

declare(strict_types=1);

use AlbaTech\Controllers\AdminController;
use AlbaTech\Controllers\CmsAdminController;
use AlbaTech\Controllers\EnquiryController;
use AlbaTech\Controllers\HomeController;
use AlbaTech\Controllers\LandingController;
use AlbaTech\Controllers\ProductController;
use AlbaTech\Controllers\SolutionsController;
use AlbaTech\Controllers\GuideController;
use AlbaTech\Controllers\GuideAdminController;
use AlbaTech\Controllers\ServiceController;
use AlbaTech\Controllers\SeoController;
use AlbaTech\Controllers\StaticPageController;
use AlbaTech\Controllers\WebhookController;
use AlbaTech\Controllers\WhatsAppAdminController;
use AlbaTech\Controllers\WorkController;
use AlbaTech\Core\Response;

/** @var \AlbaTech\Core\Application $app */
$app->router()->get('/', [new HomeController(), 'index']);
$app->router()->get('/services', [new ServiceController(), 'index']);

// 301s for URLs the previous production site had indexed, so they keep their
// authority after v2 launches. Must be registered before /services/{slug}.
$legacyServiceTargets = [
    'website-design-development' => '/services/website-design',
    'website-design-kenya' => '/services/website-design',
    'ntsa-services' => '/services/ntsa-smart-dl-assistance',
    'sha-registration' => '/services/sha-registration-help-in-kenya',
    'ecitizen-services' => '/services/ecitizen-application-assistance',
    'kra-pin-registration' => '/services/kra-returns-filing',
    'tax-compliance' => '/services/kra-returns-filing',
    'kra-nil-return' => '/services/kra-returns-filing',
    'cr12-application' => '/services/business-registration',
    'cr12-applications' => '/services/business-registration',
    'domain-registration' => '/services/website-design',
    'web-hosting' => '/services/website-design',
    'business-email-setup' => '/services/website-design',
    'software-development' => '/services/website-design',
    'graphic-design' => '/services/website-design',
    'digital-marketing' => '/services/google-business-profile-setup',
];
$legacyServiceFallbacks = [
    'nssf-services', 'it-support', 'computer-repair', 'networking', 'cctv-installation', 'wifi-networking',
];
foreach ($legacyServiceTargets as $legacySlug => $legacyTarget) {
    $app->router()->get('/services/' . $legacySlug, static function () use ($legacyTarget): void { Response::redirect($legacyTarget, 301); });
}
foreach ($legacyServiceFallbacks as $legacySlug) {
    $app->router()->get('/services/' . $legacySlug, static function (): void { Response::redirect('/services', 301); });
}
$app->router()->get('/privacy-policy', static function (): void { Response::redirect('/privacy', 301); });
$app->router()->get('/blog', static function (): void { Response::redirect('/guides', 301); });
$app->router()->get('/blog/how-to-apply-for-a-kenyan-passport-in-2026', static function (): void { Response::redirect('/guides/how-to-apply-for-a-passport-in-kenya', 301); });
$app->router()->get('/blog/{slug}', static function (): void { Response::redirect('/guides', 301); });
$app->router()->get('/faqs', static function (): void { Response::redirect('/get-help', 301); });

$landing = new LandingController();
$app->router()->get('/services/kra-ecitizen-help', [$landing, 'complianceHub']);
$app->router()->get('/services/sme-launchpad', [$landing, 'smeLaunchpad']);
$app->router()->get('/services/{slug}', [new ServiceController(), 'show']);
$work = new WorkController();
$app->router()->get('/work', [$work, 'index']);
$app->router()->get('/work/{slug}', [$work, 'show']);
$products = new ProductController();
$app->router()->get('/products', [$products, 'index']);
$app->router()->get('/products/{slug}', [$products, 'show']);
$app->router()->get('/solutions', [new SolutionsController(), 'index']);
$app->router()->get('/about', [new StaticPageController(), 'about']);
$guides = new GuideController();
$app->router()->get('/guides', [$guides, 'index']);
$app->router()->get('/guides/{slug}', [$guides, 'show']);
$app->router()->get('/get-help', [new StaticPageController(), 'getHelp']);
$app->router()->post('/get-help', [new EnquiryController(), 'submit']);
$app->router()->get('/contact', static function (): void { Response::redirect('/get-help', 301); });
$app->router()->get('/contact-details', [new StaticPageController(), 'contactDetails']);
$app->router()->get('/terms', [new StaticPageController(), 'terms']);
$app->router()->get('/privacy', [new StaticPageController(), 'privacy']);
$app->router()->get('/data-deletion', [new StaticPageController(), 'dataDeletion']);
$seo = new SeoController();
$app->router()->get('/sitemap.xml', [$seo, 'sitemap']);

$webhooks = new WebhookController();
$app->router()->get('/webhooks/whatsapp', [$webhooks, 'verify']);
$app->router()->post('/webhooks/whatsapp', [$webhooks, 'receive']);
$app->router()->get('/robots.txt', [$seo, 'robots']);
$app->router()->get('/llms.txt', [$seo, 'llms']);
$guideAdmin = new GuideAdminController();
$app->router()->get('/admin/guides', [$guideAdmin, 'index']);
$app->router()->get('/admin/guides/create', [$guideAdmin, 'create']);
$app->router()->post('/admin/guides/create', [$guideAdmin, 'save']);
$app->router()->get('/admin/guides/{id}/edit', [$guideAdmin, 'edit']);
$app->router()->post('/admin/guides/{id}/edit', [$guideAdmin, 'save']);
$app->router()->post('/admin/guides/{id}/delete', [$guideAdmin, 'delete']);
$cmsAdmin = new CmsAdminController();
$app->router()->get('/admin/cms', [$cmsAdmin, 'index']);
$app->router()->post('/admin/cms/homepage', [$cmsAdmin, 'saveHomepage']);
$app->router()->post('/admin/cms/pages', [$cmsAdmin, 'savePage']);
$app->router()->post('/admin/cms/navigation', [$cmsAdmin, 'saveNavigation']);
$app->router()->post('/admin/cms/navigation/{id}/edit', [$cmsAdmin, 'saveNavigation']);
$app->router()->post('/admin/cms/navigation/{id}/delete', [$cmsAdmin, 'deleteNavigation']);
$app->router()->post('/admin/cms/ctas', [$cmsAdmin, 'saveCta']);
$app->router()->post('/admin/cms/ctas/{id}/edit', [$cmsAdmin, 'saveCta']);
$app->router()->post('/admin/cms/ctas/{id}/delete', [$cmsAdmin, 'deleteCta']);
$admin = new AdminController();
$app->router()->get('/admin/login', [$admin, 'login']);
$app->router()->post('/admin/login', [$admin, 'login']);
$app->router()->post('/admin/logout', [$admin, 'logout']);
$app->router()->get('/admin', [$admin, 'dashboard']);
$app->router()->get('/admin/services', [$admin, 'services']);
$app->router()->get('/admin/work', [$work, 'adminIndex']);
$app->router()->get('/admin/work/create', [$work, 'adminCreate']);
$app->router()->post('/admin/work/create', [$work, 'adminSave']);
$app->router()->get('/admin/work/{id}/edit', [$work, 'adminEdit']);
$app->router()->post('/admin/work/{id}/edit', [$work, 'adminSave']);
$app->router()->post('/admin/work/{id}/delete', [$work, 'adminDelete']);
$app->router()->get('/admin/services/create', [$admin, 'serviceCreate']);
$app->router()->post('/admin/services/create', [$admin, 'serviceSave']);
$app->router()->get('/admin/services/{id}/edit', [$admin, 'serviceEdit']);
$app->router()->post('/admin/services/{id}/edit', [$admin, 'serviceSave']);
$app->router()->post('/admin/services/{id}/toggle', [$admin, 'serviceToggle']);
$app->router()->post('/admin/services/{id}/feature', [$admin, 'serviceFeature']);
$app->router()->get('/admin/categories', [$admin, 'categories']);
$app->router()->post('/admin/categories', [$admin, 'categorySave']);
$app->router()->post('/admin/categories/{id}/edit', [$admin, 'categorySave']);
$app->router()->get('/admin/settings', [$admin, 'settings']);
$app->router()->post('/admin/settings', [$admin, 'settingsSave']);
$whatsAppAdmin = new WhatsAppAdminController();
$app->router()->get('/admin/whatsapp', [$whatsAppAdmin, 'inbox']);
$app->router()->get('/admin/whatsapp/{number}', [$whatsAppAdmin, 'inbox']);
$app->router()->get('/admin/whatsapp/media/{id}', [$whatsAppAdmin, 'media']);
$app->router()->post('/admin/whatsapp/{number}/send', [$whatsAppAdmin, 'send']);
$app->router()->post('/admin/whatsapp/{number}/template', [$whatsAppAdmin, 'sendTemplate']);
$app->router()->post('/admin/whatsapp/{number}/resume-bot', [$whatsAppAdmin, 'resumeBot']);
$app->router()->post('/admin/whatsapp/{number}/delete', [$whatsAppAdmin, 'deleteConversation']);
$app->router()->post('/admin/whatsapp/test', [$whatsAppAdmin, 'sendTest']);
$app->router()->get('/health', static function (): void { Response::html('OK'); });
