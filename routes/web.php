<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\CsrfMiddleware;
use App\Core\Middleware\CustomerMiddleware;
use App\Core\Middleware\RateLimitMiddleware;
use App\Core\Middleware\RbacMiddleware;
use App\Core\Router;
use App\Modules\Admin\Controller\AuditController;
use App\Modules\Admin\Controller\DashboardController;
use App\Modules\Admin\Controller\RoleController;
use App\Modules\Admin\Controller\SecurityController;
use App\Modules\Admin\Controller\SettingsController;
use App\Modules\Admin\Controller\UserController;
use App\Modules\Auth\Controller\AuthController;
use App\Modules\Auth\Controller\GoogleAuthController;
use App\Modules\Auth\Controller\PasswordResetController;
use App\Modules\Assistant\Controller\AssistantController;
use App\Modules\Assistance\Controller\AssistanceController;
use App\Modules\Cms\Controller\BannerController;
use App\Modules\Cms\Controller\BlogController;
use App\Modules\Cms\Controller\ContactMessageController;
use App\Modules\Cms\Controller\FaqController;
use App\Modules\Cms\Controller\MediaController;
use App\Modules\Cms\Controller\MenuController;
use App\Modules\Cms\Controller\PageController;
use App\Modules\Cms\Controller\PublicSiteController;
use App\Modules\Cms\Controller\ServiceController;
use App\Modules\Cms\Controller\TestimonialController;
use App\Modules\Customer\Controller\CustomerController;
use App\Modules\Growth\Controller\LeadController;
use App\Modules\Growth\Controller\IntelligenceController;
use App\Modules\Growth\Controller\ProjectController;
use App\Modules\System\Controller\HealthController;

/** @var Router $router */

// Infrastructure health endpoints. They never expose exception details or configuration.
$router->get('/healthz', [HealthController::class, 'live']);
$router->get('/readyz', [HealthController::class, 'ready']);

$loginPath = Config::get('auth.login_path', '/login');
$adminPath = Config::get('admin.path', '/admin');

// Global CSRF protection on all state-changing requests.
$router->group('', [CsrfMiddleware::class], function (Router $router) use ($loginPath, $adminPath) {

    // --- Guest routes ---------------------------------------------------
    $router->get($loginPath, [AuthController::class, 'showLogin']);
    $router->post($loginPath, [AuthController::class, 'login'], [RateLimitMiddleware::class . ':login']);
    $router->get($loginPath . '/verify', [AuthController::class, 'showTwoFactorChallenge']);
    $router->post($loginPath . '/verify', [AuthController::class, 'verifyTwoFactorChallenge'], [RateLimitMiddleware::class . ':2fa']);
    $router->get('/forgot-password', [PasswordResetController::class, 'showForgotForm']);
    $router->post('/forgot-password', [PasswordResetController::class, 'sendResetLink'], [RateLimitMiddleware::class . ':password-reset']);
    $router->get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm']);
    $router->post('/reset-password', [PasswordResetController::class, 'resetPassword'], [RateLimitMiddleware::class . ':password-reset']);
    $router->get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register'], [RateLimitMiddleware::class . ':register']);
    $router->get('/auth/google/redirect', [GoogleAuthController::class, 'start']);
    $router->get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
    $router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

    // --- Public digital assistant ---------------------------------------
    $router->get('/assistant', [AssistantController::class, 'index']);
    $router->post('/assistant/start', [AssistantController::class, 'start'], [RateLimitMiddleware::class . ':assistant-start']);
    $router->post('/assistant/message', [AssistantController::class, 'message'], [RateLimitMiddleware::class . ':assistant']);
    // Anonymous first-party analytics collection. CSRF-protected and stores no raw IP/user-agent.
    $router->post('/analytics/collect', [IntelligenceController::class, 'collect'], [RateLimitMiddleware::class . ':analytics']);
    $router->post('/analytics/event', [IntelligenceController::class, 'event'], [RateLimitMiddleware::class . ':analytics']);

    // --- Authenticated routes --------------------------------------------
    $router->group('/dashboard', [AuthMiddleware::class], function (Router $router) {
        $router->get('', [DashboardController::class, 'index']);
    });

    // Any authenticated user can manage their own 2FA.
    $router->group('', [AuthMiddleware::class], function (Router $router) use ($adminPath) {
        $router->get($adminPath . '/security/2fa', [SecurityController::class, 'show']);
        $router->post($adminPath . '/security/2fa/start', [SecurityController::class, 'startSetup']);
        $router->post($adminPath . '/security/2fa/confirm', [SecurityController::class, 'confirmSetup']);
        $router->get($adminPath . '/security/2fa/recovery-codes', [SecurityController::class, 'showRecoveryCodes']);
        $router->post($adminPath . '/security/2fa/disable', [SecurityController::class, 'disable']);
        $router->post('/resend-verification', [AuthController::class, 'resendVerification']);
    });

    // Customer account area. CustomerMiddleware prevents staff/admin accounts from entering it.
    $router->group('/account', [CustomerMiddleware::class], function (Router $router) {
        $router->get('', [CustomerController::class, 'dashboard']);
        $router->get('/requests', [CustomerController::class, 'requests']);
        $router->get('/requests/{id}', [CustomerController::class, 'requestShow']);
        $router->get('/quotes', [CustomerController::class, 'quotes']);
        $router->get('/quotes/{id}', [CustomerController::class, 'quoteShow']);
        $router->get('/payments', [CustomerController::class, 'payments']);
        $router->get('/profile', [CustomerController::class, 'profile']);
        $router->post('/profile', [CustomerController::class, 'updateProfile']);
    });

    // Customer work is intentionally handled outside the authenticated order/checkout flow.

    // --- Admin panel: RBAC-gated by permission slug ----------------------
    $router->group($adminPath, [AuthMiddleware::class], function (Router $router) {
        $router->get('/settings', [SettingsController::class, 'edit'], [RbacMiddleware::class . ':settings.manage']);
        $router->post('/settings', [SettingsController::class, 'update'], [RbacMiddleware::class . ':settings.manage']);

        $router->get('/users', [UserController::class, 'index'], [RbacMiddleware::class . ':users.view']);
        $router->get('/users/create', [UserController::class, 'create'], [RbacMiddleware::class . ':users.manage']);
        $router->post('/users', [UserController::class, 'store'], [RbacMiddleware::class . ':users.manage']);
        $router->post('/users/{id}/toggle-active', [UserController::class, 'toggleActive'], [RbacMiddleware::class . ':users.manage']);

        $router->get('/roles', [RoleController::class, 'index'], [RbacMiddleware::class . ':roles.view']);
        $router->get('/roles/{id}/edit', [RoleController::class, 'edit'], [RbacMiddleware::class . ':roles.manage']);
        $router->post('/roles/{id}', [RoleController::class, 'update'], [RbacMiddleware::class . ':roles.manage']);

        $router->get('/audit', [AuditController::class, 'index'], [RbacMiddleware::class . ':audit.view']);

        // --- CMS: Pages ---------------------------------------------------
        $router->get('/pages', [PageController::class, 'index'], [RbacMiddleware::class . ':pages.view']);
        $router->get('/pages/create', [PageController::class, 'create'], [RbacMiddleware::class . ':pages.manage']);
        $router->post('/pages', [PageController::class, 'store'], [RbacMiddleware::class . ':pages.manage']);
        $router->get('/pages/{id}/edit', [PageController::class, 'edit'], [RbacMiddleware::class . ':pages.manage']);
        $router->post('/pages/{id}', [PageController::class, 'update'], [RbacMiddleware::class . ':pages.manage']);
        $router->post('/pages/{id}/delete', [PageController::class, 'destroy'], [RbacMiddleware::class . ':pages.manage']);

        // --- CMS: Blog ------------------------------------------------------
        $router->get('/blog', [BlogController::class, 'index'], [RbacMiddleware::class . ':blog.view']);
        $router->get('/blog/create', [BlogController::class, 'create'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog', [BlogController::class, 'store'], [RbacMiddleware::class . ':blog.manage']);
        $router->get('/blog/{id}/edit', [BlogController::class, 'edit'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog/{id}', [BlogController::class, 'update'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog/{id}/delete', [BlogController::class, 'destroy'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog-categories', [BlogController::class, 'storeCategory'], [RbacMiddleware::class . ':blog.manage']);

        // --- CMS: Menus -----------------------------------------------------
        $router->get('/menus/{slug}', [MenuController::class, 'edit'], [RbacMiddleware::class . ':menus.manage']);
        $router->post('/menus/{slug}/items', [MenuController::class, 'storeItem'], [RbacMiddleware::class . ':menus.manage']);
        $router->post('/menu-items/{id}', [MenuController::class, 'updateItem'], [RbacMiddleware::class . ':menus.manage']);
        $router->post('/menu-items/{id}/delete', [MenuController::class, 'destroyItem'], [RbacMiddleware::class . ':menus.manage']);

        // --- CMS: Banners -----------------------------------------------------
        $router->get('/banners', [BannerController::class, 'index'], [RbacMiddleware::class . ':banners.manage']);
        $router->get('/banners/create', [BannerController::class, 'create'], [RbacMiddleware::class . ':banners.manage']);
        $router->post('/banners', [BannerController::class, 'store'], [RbacMiddleware::class . ':banners.manage']);
        $router->get('/banners/{id}/edit', [BannerController::class, 'edit'], [RbacMiddleware::class . ':banners.manage']);
        $router->post('/banners/{id}', [BannerController::class, 'update'], [RbacMiddleware::class . ':banners.manage']);
        $router->post('/banners/{id}/delete', [BannerController::class, 'destroy'], [RbacMiddleware::class . ':banners.manage']);

        // --- CMS: FAQs ------------------------------------------------------
        $router->get('/faqs', [FaqController::class, 'index'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs', [FaqController::class, 'store'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs/{id}', [FaqController::class, 'update'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs/{id}/delete', [FaqController::class, 'destroy'], [RbacMiddleware::class . ':faqs.manage']);

        // --- CMS: Testimonials -------------------------------------------------
        $router->get('/testimonials', [TestimonialController::class, 'index'], [RbacMiddleware::class . ':testimonials.manage']);
        $router->post('/testimonials', [TestimonialController::class, 'store'], [RbacMiddleware::class . ':testimonials.manage']);
        $router->post('/testimonials/{id}', [TestimonialController::class, 'update'], [RbacMiddleware::class . ':testimonials.manage']);
        $router->post('/testimonials/{id}/delete', [TestimonialController::class, 'destroy'], [RbacMiddleware::class . ':testimonials.manage']);

        // --- CMS: Services ---------------------------------------------------
        $router->get('/services', [ServiceController::class, 'index'], [RbacMiddleware::class . ':services.view']);
        $router->get('/services/create', [ServiceController::class, 'create'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services', [ServiceController::class, 'store'], [RbacMiddleware::class . ':services.manage']);
        $router->get('/services/{id}/edit', [ServiceController::class, 'edit'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}', [ServiceController::class, 'update'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/toggle-status', [ServiceController::class, 'toggleStatus'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/toggle-homepage', [ServiceController::class, 'toggleHomepage'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/delete', [ServiceController::class, 'destroy'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/service-categories', [ServiceController::class, 'storeCategory'], [RbacMiddleware::class . ':services.manage']);

        // --- CMS: Media library -------------------------------------------------
        $router->get('/media', [MediaController::class, 'index'], [RbacMiddleware::class . ':media.manage']);
        $router->post('/media', [MediaController::class, 'store'], [RbacMiddleware::class . ':media.manage']);
        $router->post('/media/{id}/delete', [MediaController::class, 'destroy'], [RbacMiddleware::class . ':media.manage']);

        // --- Contact messages -------------------------------------------------
        $router->get('/contact-messages', [ContactMessageController::class, 'index'], [RbacMiddleware::class . ':contact_messages.view']);
        $router->post('/contact-messages/{id}/read', [ContactMessageController::class, 'markRead'], [RbacMiddleware::class . ':contact_messages.view']);

        // Legacy orders/payments are intentionally decommissioned; customer conversion is WhatsApp-first.

        // --- Growth: Leads -----------------------------------------------------
        $router->get('/growth/intelligence', [IntelligenceController::class, 'dashboard'], [RbacMiddleware::class . ':growth.intelligence.view']);
        $router->post('/growth/intelligence/notes/{id}/dismiss', [IntelligenceController::class, 'dismiss'], [RbacMiddleware::class . ':growth.intelligence.manage']);

        $router->get('/leads', [LeadController::class, 'index'], [RbacMiddleware::class . ':leads.view']);
        $router->get('/leads/{id}', [LeadController::class, 'show'], [RbacMiddleware::class . ':leads.view']);
        $router->post('/leads/{id}', [LeadController::class, 'update'], [RbacMiddleware::class . ':leads.manage']);

        // --- Assistance desk ---------------------------------------------------
        $router->get('/assistance', [AssistanceController::class, 'index'], [RbacMiddleware::class . ':assistance.view']);
        $router->get('/assistance/payments', [AssistanceController::class, 'payments'], [RbacMiddleware::class . ':assistance.payments.manage']);
        $router->get('/assistance/reviews', [AssistanceController::class, 'reviews'], [RbacMiddleware::class . ':assistance.reviews.manage']);
        $router->get('/assistance/notifications', [AssistanceController::class, 'notifications'], [RbacMiddleware::class . ':assistance.notifications.view']);
        $router->get('/assistant/sessions', [AssistantController::class, 'sessions'], [RbacMiddleware::class . ':assistant.sessions.view']);
        $router->get('/assistant/sessions/{id}', [AssistantController::class, 'session'], [RbacMiddleware::class . ':assistant.sessions.view']);
        $router->post('/assistance/notifications/{id}/retry', [AssistanceController::class, 'notificationRetry'], [RbacMiddleware::class . ':assistance.notifications.manage']);
        $router->post('/assistance/reviews/{id}', [AssistanceController::class, 'reviewModerate'], [RbacMiddleware::class . ':assistance.reviews.manage']);
        $router->get('/assistance/{id}/work', [AssistanceController::class, 'work'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->post('/assistance/{id}/assign', [AssistanceController::class, 'assign'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->post('/assistance/{id}/tasks', [AssistanceController::class, 'taskStore'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->post('/assistance/tasks/{taskId}', [AssistanceController::class, 'taskUpdate'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->post('/assistance/{id}/updates', [AssistanceController::class, 'updateStore'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->post('/assistance/{id}/complete', [AssistanceController::class, 'complete'], [RbacMiddleware::class . ':assistance.work.manage']);
        $router->get('/assistance/{id}', [AssistanceController::class, 'show'], [RbacMiddleware::class . ':assistance.view']);
        $router->post('/assistance/{id}', [AssistanceController::class, 'update'], [RbacMiddleware::class . ':assistance.manage']);
        $router->get('/assistance/{id}/quote/create', [AssistanceController::class, 'quoteCreate'], [RbacMiddleware::class . ':assistance.quotes.manage']);
        $router->post('/assistance/{id}/quote', [AssistanceController::class, 'quoteStore'], [RbacMiddleware::class . ':assistance.quotes.manage']);
        $router->get('/assistance/quote/{id}', [AssistanceController::class, 'quoteShow'], [RbacMiddleware::class . ':assistance.quotes.manage']);
        $router->post('/assistance/payments/{id}/verify', [AssistanceController::class, 'paymentVerify'], [RbacMiddleware::class . ':assistance.payments.manage']);
        $router->post('/assistance/payments/{id}/reject', [AssistanceController::class, 'paymentReject'], [RbacMiddleware::class . ':assistance.payments.manage']);

        // --- Growth: Portfolio -------------------------------------------------
        $router->get('/projects', [ProjectController::class, 'index'], [RbacMiddleware::class . ':projects.view']);
        $router->get('/projects/create', [ProjectController::class, 'create'], [RbacMiddleware::class . ':projects.manage']);
        $router->post('/projects', [ProjectController::class, 'store'], [RbacMiddleware::class . ':projects.manage']);
        $router->get('/projects/{id}/edit', [ProjectController::class, 'edit'], [RbacMiddleware::class . ':projects.manage']);
        $router->post('/projects/{id}', [ProjectController::class, 'update'], [RbacMiddleware::class . ':projects.manage']);
        $router->post('/projects/{id}/delete', [ProjectController::class, 'destroy'], [RbacMiddleware::class . ':projects.manage']);

    });
});

// --- Public-facing site (no auth) ---------------------------------------

$router->get('/', [PublicSiteController::class, 'home']);
$router->get('/blog', [PublicSiteController::class, 'blogIndex']);
$router->get('/blog/{slug}', [PublicSiteController::class, 'blogShow']);
$router->get('/faqs', [PublicSiteController::class, 'faqsPage']);
$router->get('/services', [PublicSiteController::class, 'servicesIndex']);
$router->get('/services/{slug}', [PublicSiteController::class, 'serviceShow']);
$router->get('/get-help', [AssistanceController::class, 'create']);
$router->post('/get-help', [AssistanceController::class, 'store'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':assistance']);
$router->get('/get-help/thanks', [AssistanceController::class, 'thanks']);
$router->get('/quote/{token}', [AssistanceController::class, 'quotePublic']);
$router->get('/request/{token}', [AssistanceController::class, 'portal']);
$router->get('/request/{token}/notifications', [AssistanceController::class, 'notificationPreferences']);
$router->post('/request/{token}/notifications', [AssistanceController::class, 'updateNotificationPreferences']);
$router->get('/review/{token}', [AssistanceController::class, 'review']);
$router->get('/receipt/{token}', [AssistanceController::class, 'receipt']);
$router->post('/review/{token}', [AssistanceController::class, 'reviewStore'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':review']);
$router->post('/quote/{token}/accept', [AssistanceController::class, 'quoteAccept'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':quote']);
$router->post('/quote/{token}/payment', [AssistanceController::class, 'quotePayment'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':quote-payment']);
$router->get('/about', [PublicSiteController::class, 'aboutPage']);
$router->get('/contact', [PublicSiteController::class, 'contactPage']);
$router->get('/projects', [PublicSiteController::class, 'projectsIndex']);
$router->get('/projects/{slug}', [PublicSiteController::class, 'projectShow']);
$router->post('/contact', [PublicSiteController::class, 'contactSubmit'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':contact']);
$router->get('/robots.txt', [PublicSiteController::class, 'robotsTxt']);
$router->get('/sitemap.xml', [PublicSiteController::class, 'sitemapXml']);
$router->get('/{slug}', [PublicSiteController::class, 'page']);

