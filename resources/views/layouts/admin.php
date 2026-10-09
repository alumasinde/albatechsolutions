<?php

use AlbaTech\Core\View;

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin'; 
/**
 * @var string $pageTitle
 * @var string $csrf
 * @var string $cspNonce
 * @var array $adminUser
 * @var string $currentPath
 * @var string $content
 */
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= View::escape($pageTitle ?? 'Admin') ?> | AlbaTech Admin</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23155eef'/%3E%3Ctext x='16' y='23' font-family='Arial' font-size='20' font-weight='700' text-anchor='middle' fill='%23fff'%3EA%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body>
    <div class="admin-shell">
        <aside class="sidebar"><a class="brand" href="/admin"><span class="brand-mark">A</span><span><strong>AlbaTech</strong><small>Website Admin</small></span></a>
            <div class="nav-label">WORKSPACE</div>
            <nav class="admin-nav"><a class="<?= $currentPath === '/admin' ? 'active' : '' ?>" href="/admin"><i class="fa-solid fa-chart-pie"></i> Overview</a><a class="<?= str_starts_with($currentPath, '/admin/services') ? 'active' : '' ?>" href="/admin/services"><i class="fa-solid fa-layer-group"></i> Services</a><a class="<?= str_starts_with($currentPath, '/admin/categories') ? 'active' : '' ?>" href="/admin/categories"><i class="fa-solid fa-folder-tree"></i> Categories</a><a class="<?= str_starts_with($currentPath, '/admin/settings') ? 'active' : '' ?>" href="/admin/settings"><i class="fa-solid fa-sliders"></i> Site settings</a></nav>
            <div class="sidebar-bottom"><a href="/" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> View website</a>
                <form method="post" action="/admin/logout"><input type="hidden" name="_csrf" value="<?= View::escape($csrf ?? '') ?>"><button type="submit" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Sign out</button></form>
            </div>
        </aside>
        <main class="main-area">
            <header class="topbar">
                <div><span class="eyebrow">ALBATECH SOLUTIONS</span>
                    <h1><?= View::escape($pageTitle ?? 'Overview') ?></h1>
                </div>
                <div class="admin-identity"><span class="avatar"><?= View::escape(strtoupper(substr((string)($adminUser['first_name'] ?? 'A'), 0, 1))) ?></span><span><strong><?= View::escape(($adminUser['first_name'] ?? 'Admin') . ' ' . ($adminUser['last_name'] ?? '')) ?></strong><small>Administrator</small></span></div>
            </header>
            <section class="page-content"><?php if (!empty($flash)): ?><div class="flash flash-<?= View::escape($flash['type'] ?? 'success') ?>"><i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i><?= View::escape($flash['message'] ?? '') ?><button type="button" aria-label="Dismiss" data-dismiss-flash>&times;</button></div><?php endif; ?><?= $content ?></section>
        </main>
    </div>
    <script nonce="<?= View::escape($cspNonce ?? '') ?>">
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('submit', function(e) {
                if (!confirm(el.dataset.confirm)) {
                    e.preventDefault();
                }
            })
        });
        document.querySelectorAll('[data-dismiss-flash]').forEach(function(button) {
            button.addEventListener('click', function() {
                button.parentElement.remove();
            });
        });
        document.querySelectorAll('.flash').forEach(function(el) {
            setTimeout(function() {
                el.classList.add('flash-out');
                setTimeout(function() {
                    el.remove()
                }, 350)
            }, 5000)
        });
    </script>
</body>

</html>