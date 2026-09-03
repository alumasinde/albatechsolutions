<?php ob_start(); ?>
<div class="page-wrapper">
    <header class="page-header">
        <h1>Welcome, <?= e($user['name'] ?? 'User') ?></h1>
        <form method="POST" action="/logout">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </header>

    <?php if (\App\Core\Auth::can('settings.manage') || \App\Core\Auth::can('users.view')): ?>
        <p><a href="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/settings" class="btn btn-primary">
            <i class="fa-solid fa-gauge"></i> Go to Admin Panel
        </a></p>
    <?php endif; ?>

    <p>This is a scaffold dashboard — module widgets (orders, invoices, tickets, appointments) render here.</p>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
