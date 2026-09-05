<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\CsrfMiddleware;
use App\Core\Middleware\RateLimitMiddleware;
use App\Core\Middleware\RbacMiddleware;
use App\Core\Middleware\TokenRateLimitMiddleware;
use App\Core\Router;
use App\Modules\Admin\Controller\AuditController;
use App\Modules\Admin\Controller\DashboardController;
use App\Modules\Admin\Controller\RoleController;
use App\Modules\Admin\Controller\SecurityController;
use App\Modules\Admin\Controller\SettingsController;
use App\Modules\Admin\Controller\UserController;
use App\Modules\Auth\Controller\AuthController;
use App\Modules\Auth\Controller\PasswordResetController;
use App\Modules\Assistance\Controller\AssistanceController;
use App\Modules\Cms\Controller\BlogController;
use App\Modules\Cms\Controller\ContactMessageController;
use App\Modules\Cms\Controller\FaqController;
use App\Modules\Cms\Controller\MediaController;
use App\Modules\Cms\Controller\PublicSiteController;
use App\Modules\Cms\Controller\ServiceController;
use App\Modules\System\Controller\GitHubWebhookController;
use App\Modules\System\Controller\HealthController;
use App\Modules\Growth\Controller\IntelligenceController;

/** @var Router $router */

$router->get('/healthz', [HealthController::class, 'live']);
$router->get('/readyz', [HealthController::class, 'ready']);

$loginPath = Config::get('auth.login_path', '/login');
$adminPath = Config::get('admin.path', '/admin');

$router->group('', [CsrfMiddleware::class], function (Router $router) use ($loginPath, $adminPath) {
    $router->get($loginPath, [AuthController::class, 'showLogin']);
    $router->post($loginPath, [AuthController::class, 'login'], [RateLimitMiddleware::class . ':login']);
    $router->get($loginPath . '/verify', [AuthController::class, 'showTwoFactorChallenge']);
    $router->post($loginPath . '/verify', [AuthController::class, 'verifyTwoFactorChallenge'], [RateLimitMiddleware::class . ':2fa']);
    $router->get('/forgot-password', [PasswordResetController::class, 'showForgotForm']);
    $router->post('/forgot-password', [PasswordResetController::class, 'sendResetLink'], [RateLimitMiddleware::class . ':password-reset']);
    $router->get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm']);
    $router->post('/reset-password', [PasswordResetController::class, 'resetPassword'], [RateLimitMiddleware::class . ':password-reset']);
    $router->get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);

    $router->group('/dashboard', [AuthMiddleware::class], function (Router $router) {
        $router->get('', [DashboardController::class, 'index']);
    });

    $router->group('', [AuthMiddleware::class], function (Router $router) use ($adminPath) {
        $router->get($adminPath . '/security/2fa', [SecurityController::class, 'show']);
        $router->post($adminPath . '/security/2fa/start', [SecurityController::class, 'startSetup']);
        $router->post($adminPath . '/security/2fa/confirm', [SecurityController::class, 'confirmSetup']);
        $router->get($adminPath . '/security/2fa/recovery-codes', [SecurityController::class, 'showRecoveryCodes']);
        $router->post($adminPath . '/security/2fa/disable', [SecurityController::class, 'disable']);
        $router->post('/resend-verification', [AuthController::class, 'resendVerification']);
    });

    $router->group($adminPath, [AuthMiddleware::class, AdminMiddleware::class], function (Router $router) {
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
        $router->get('/blog', [BlogController::class, 'index'], [RbacMiddleware::class . ':blog.view']);
        $router->get('/blog/create', [BlogController::class, 'create'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog', [BlogController::class, 'store'], [RbacMiddleware::class . ':blog.manage']);
        $router->get('/blog/{id}/edit', [BlogController::class, 'edit'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog/{id}', [BlogController::class, 'update'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog/{id}/delete', [BlogController::class, 'destroy'], [RbacMiddleware::class . ':blog.manage']);
        $router->post('/blog-categories', [BlogController::class, 'storeCategory'], [RbacMiddleware::class . ':blog.manage']);
        $router->get('/faqs', [FaqController::class, 'index'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs', [FaqController::class, 'store'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs/{id}', [FaqController::class, 'update'], [RbacMiddleware::class . ':faqs.manage']);
        $router->post('/faqs/{id}/delete', [FaqController::class, 'destroy'], [RbacMiddleware::class . ':faqs.manage']);
        $router->get('/services', [ServiceController::class, 'index'], [RbacMiddleware::class . ':services.view']);
        $router->get('/services/create', [ServiceController::class, 'create'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services', [ServiceController::class, 'store'], [RbacMiddleware::class . ':services.manage']);
        $router->get('/services/{id}/edit', [ServiceController::class, 'edit'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}', [ServiceController::class, 'update'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/toggle-status', [ServiceController::class, 'toggleStatus'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/toggle-homepage', [ServiceController::class, 'toggleHomepage'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/services/{id}/delete', [ServiceController::class, 'destroy'], [RbacMiddleware::class . ':services.manage']);
        $router->post('/service-categories', [ServiceController::class, 'storeCategory'], [RbacMiddleware::class . ':services.manage']);
        $router->get('/media', [MediaController::class, 'index'], [RbacMiddleware::class . ':media.manage']);
        $router->post('/media', [MediaController::class, 'store'], [RbacMiddleware::class . ':media.manage']);
        $router->post('/media/{id}/delete', [MediaController::class, 'destroy'], [RbacMiddleware::class . ':media.manage']);
        $router->get('/contact-messages', [ContactMessageController::class, 'index'], [RbacMiddleware::class . ':contact_messages.view']);
        $router->post('/contact-messages/{id}/read', [ContactMessageController::class, 'markRead'], [RbacMiddleware::class . ':contact_messages.view']);
        $router->get('/assistance', [AssistanceController::class, 'index'], [RbacMiddleware::class . ':assistance.view']);
        $router->get('/assistance/payments', [AssistanceController::class, 'payments'], [RbacMiddleware::class . ':assistance.payments.manage']);
        $router->get('/assistance/reviews', [AssistanceController::class, 'reviews'], [RbacMiddleware::class . ':assistance.reviews.manage']);
        $router->get('/assistance/notifications', [AssistanceController::class, 'notifications'], [RbacMiddleware::class . ':assistance.notifications.view']);
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
    });
});

// GitHub-signed deployment endpoint; deliberately outside browser CSRF.
$router->post('/webhooks/github', [GitHubWebhookController::class, '__invoke']);

// Public analytics endpoints. JSON beacon/fetch requests do not rely on browser form CSRF.
$router->post('/analytics/collect', [IntelligenceController::class, 'collect']);
$router->post('/analytics/event', [IntelligenceController::class, 'event']);

// --- Public-facing site -------------------------------------------------
$router->get('/', [PublicSiteController::class, 'home']);
$router->get('/blog', [PublicSiteController::class, 'blogIndex']);
$router->get('/blog/{slug}', [PublicSiteController::class, 'blogShow']);
$router->get('/faqs', [PublicSiteController::class, 'faqsPage']);
$router->get('/services', [PublicSiteController::class, 'servicesIndex']);
$router->get('/services/website-design-development', [PublicSiteController::class, 'redirectServiceLegacyWebsiteDesign']);
$router->get('/services/cr12-applications', [PublicSiteController::class, 'redirectServiceLegacyCr12']);
$router->get('/services/networking', [PublicSiteController::class, 'redirectServiceLegacyNetworking']);
$router->get('/services/{slug}', [PublicSiteController::class, 'serviceShow']);
$router->get('/get-help', [AssistanceController::class, 'create']);
$router->post('/get-help', [AssistanceController::class, 'store'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':assistance']);
$router->get('/get-help/thanks', [AssistanceController::class, 'thanks']);
$router->get('/quote/{token}', [AssistanceController::class, 'quotePublic'], [TokenRateLimitMiddleware::class . ':quote']);
$router->get('/request/{token}', [AssistanceController::class, 'portal'], [TokenRateLimitMiddleware::class . ':request']);
$router->get('/request/{token}/notifications', [AssistanceController::class, 'notificationPreferences'], [TokenRateLimitMiddleware::class . ':request-notifications']);
$router->post('/request/{token}/notifications', [AssistanceController::class, 'updateNotificationPreferences'], [CsrfMiddleware::class, TokenRateLimitMiddleware::class . ':request-notifications']);
$router->get('/review/{token}', [AssistanceController::class, 'review'], [TokenRateLimitMiddleware::class . ':review']);
$router->get('/receipt/{token}', [AssistanceController::class, 'receipt'], [TokenRateLimitMiddleware::class . ':receipt']);
$router->post('/review/{token}', [AssistanceController::class, 'reviewStore'], [CsrfMiddleware::class, TokenRateLimitMiddleware::class . ':review']);
$router->post('/quote/{token}/accept', [AssistanceController::class, 'quoteAccept'], [CsrfMiddleware::class, TokenRateLimitMiddleware::class . ':quote-accept']);
$router->post('/quote/{token}/payment', [AssistanceController::class, 'quotePayment'], [CsrfMiddleware::class, TokenRateLimitMiddleware::class . ':quote-payment']);
$router->get('/about', [PublicSiteController::class, 'aboutPage']);
$router->get('/contact', [PublicSiteController::class, 'contactPage']);
$router->post('/contact', [PublicSiteController::class, 'contactSubmit'], [CsrfMiddleware::class, RateLimitMiddleware::class . ':contact']);
$router->get('/robots.txt', [PublicSiteController::class, 'robotsTxt']);
$router->get('/sitemap.xml', [PublicSiteController::class, 'sitemapXml']);
