<?php
use App\Core\Auth;
use App\Core\Config;

$adminPath = Config::get('admin.path', '/admin');

ob_start();
?>
<div class="admin-topbar">
    <div class="admin-brand"><i class="fa-solid fa-shield-halved"></i> <?= e(setting('site_name', 'AlbaTech')) ?></div>
    <button type="button" class="nav-toggle" id="admin-nav-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="admin-sidebar">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>
<div class="sidebar-overlay" id="admin-sidebar-overlay"></div>
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-brand"><i class="fa-solid fa-shield-halved"></i> <?= e(setting('site_name', 'AlbaTech')) ?></div>
        <nav>
            <a href="/dashboard"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <?php if (Auth::can('settings.manage')): ?>
                <a href="<?= e($adminPath) ?>/settings"><i class="fa-solid fa-palette"></i> Settings</a>
            <?php endif; ?>
            <?php if (Auth::can('services.view')): ?>
                <a href="<?= e($adminPath) ?>/services"><i class="fa-solid fa-briefcase"></i> Services</a>
            <?php endif; ?>
            <?php if (Auth::can('assistance.view')): ?>
                <a href="<?= e($adminPath) ?>/assistance"><i class="fa-solid fa-hand-holding-heart"></i> Help Requests</a>
            <?php endif; ?>
            <?php if (Auth::can('assistance.payments.manage')): ?>
                <a href="<?= e($adminPath) ?>/assistance/payments"><i class="fa-solid fa-money-bill-transfer"></i> Assistance Payments</a>
            <?php endif; ?>
            <?php if (Auth::can('assistance.reviews.manage')): ?>
                <a href="<?= e($adminPath) ?>/assistance/reviews"><i class="fa-solid fa-star"></i> Assistance Reviews</a>
            <?php endif; ?>
            <?php if (Auth::can('assistance.notifications.view')): ?>
                <a href="<?= e($adminPath) ?>/assistance/notifications"><i class="fa-solid fa-paper-plane"></i> Notifications</a>
            <?php endif; ?>
            <?php if (Auth::can('pages.view')): ?>
                <a href="<?= e($adminPath) ?>/pages"><i class="fa-solid fa-file-lines"></i> Pages</a>
            <?php endif; ?>
            <?php if (Auth::can('blog.view')): ?>
                <a href="<?= e($adminPath) ?>/blog"><i class="fa-solid fa-newspaper"></i> Blog</a>
            <?php endif; ?>
            <?php if (Auth::can('menus.manage')): ?>
                <a href="<?= e($adminPath) ?>/menus/header"><i class="fa-solid fa-bars"></i> Menus</a>
            <?php endif; ?>
            <?php if (Auth::can('banners.manage')): ?>
                <a href="<?= e($adminPath) ?>/banners"><i class="fa-solid fa-panorama"></i> Banners</a>
            <?php endif; ?>
            <?php if (Auth::can('faqs.manage')): ?>
                <a href="<?= e($adminPath) ?>/faqs"><i class="fa-solid fa-circle-question"></i> FAQs</a>
            <?php endif; ?>
            <?php if (Auth::can('testimonials.manage')): ?>
                <a href="<?= e($adminPath) ?>/testimonials"><i class="fa-solid fa-quote-left"></i> Testimonials</a>
            <?php endif; ?>
            <?php if (Auth::can('media.manage')): ?>
                <a href="<?= e($adminPath) ?>/media"><i class="fa-solid fa-photo-film"></i> Media Library</a>
            <?php endif; ?>
            <?php if (Auth::can('contact_messages.view')): ?>
                <a href="<?= e($adminPath) ?>/contact-messages"><i class="fa-solid fa-envelope-open-text"></i> Contact Messages</a>
            <?php endif; ?>
            <?php if (Auth::can('users.view')): ?>
                <a href="<?= e($adminPath) ?>/users"><i class="fa-solid fa-users"></i> Users</a>
            <?php endif; ?>
            <?php if (Auth::can('roles.view')): ?>
                <a href="<?= e($adminPath) ?>/roles"><i class="fa-solid fa-user-shield"></i> Roles</a>
            <?php endif; ?>
            <?php if (Auth::can('audit.view')): ?>
                <a href="<?= e($adminPath) ?>/audit"><i class="fa-solid fa-clock-rotate-left"></i> Audit Log</a>
            <?php endif; ?>
            <a href="<?= e($adminPath) ?>/security/2fa"><i class="fa-solid fa-key"></i> Two-Factor Auth</a>
        </nav>
    </aside>
    <main class="admin-content">
        <?php $success = \App\Core\Session::getFlash('_success'); ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?= $adminContent ?? '' ?>
    </main>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
