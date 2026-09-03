<?php
use App\Core\Config;
use App\Core\Auth;
$title = $title ?? 'My AlbaTech Account';
ob_start();
?>
<div class="customer-shell">
    <header class="customer-topbar">
        <a href="/" class="customer-brand"><?= e(setting('site_name','AlbaTech Solutions')) ?></a>
        <nav class="customer-nav" aria-label="Account navigation">
            <a href="/account">Overview</a>
            <a href="/account/requests">My Requests</a>
            <a href="/account/quotes">Quotes</a>
            <a href="/account/payments">Payments</a>
            <a href="/account/profile">Profile</a>
            <form method="POST" action="/logout" class="inline-form"><?= csrf_field() ?><button type="submit">Log out</button></form>
        </nav>
    </header>
    <main class="customer-main">
        <?php if($success=\App\Core\Session::getFlash('_success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <?= $customerContent ?? '' ?>
    </main>
</div>
<?php
$content=ob_get_clean();
require dirname(__DIR__).'/layouts/app.php';
