<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-briefcase"></i> Services</h1>

<p style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?= e($adminPath) ?>/services/create" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Service</a>
</p>

<details class="card" style="margin-bottom:20px;">
    <summary style="cursor:pointer;font-weight:600;">Add category</summary>
    <form method="POST" action="<?= e($adminPath) ?>/service-categories" style="margin-top:12px;">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Category name" required>
        <button type="submit" class="btn btn-secondary" style="margin-top:10px;">Add</button>
    </form>
</details>

<table class="table">
    <thead><tr><th>Service</th><th>Category</th><th>Price</th><th>Status</th><th>Homepage</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($services as $s): ?>
        <tr>
            <td><?= e($s['name']) ?></td>
            <td><?= e($s['category_name'] ?? '—') ?></td>
            <td>
                <?php if ($s['price_type'] === 'quote'): ?>
                    Quote on request
                <?php else: ?>
                    <?= $s['price_type'] === 'starting_from' ? 'From ' : '' ?>KES <?= number_format((float) $s['price'], 0) ?>
                <?php endif; ?>
            </td>
            <?php $isActive = $s['status'] === 'published'; ?>
            <td>
                <div class="service-admin-toggles">
                    <form method="POST" action="<?= e($adminPath) ?>/services/<?= e((string) $s['id']) ?>/toggle-status" class="service-toggle-form">
                        <?= csrf_field() ?>
                        <button
                            type="submit"
                            class="service-toggle service-status-toggle <?= $isActive ? 'is-on' : '' ?>"
                            aria-pressed="<?= $isActive ? 'true' : 'false' ?>"
                            aria-label="<?= $isActive ? 'Deactivate' : 'Activate' ?> <?= e($s['name']) ?>"
                            title="<?= $isActive ? 'Deactivate service' : 'Activate service' ?>"
                        >
                            <span class="service-toggle__track" aria-hidden="true"><span class="service-toggle__thumb"></span></span>
                            <span class="service-toggle__label"><?= $isActive ? 'Active' : 'Inactive' ?></span>
                        </button>
                    </form>
                </div>
            </td>
            <td>
                <?php $isHomepage = (int) ($s['is_featured'] ?? 0) === 1; ?>
                <form method="POST" action="<?= e($adminPath) ?>/services/<?= e((string) $s['id']) ?>/toggle-homepage" class="service-toggle-form">
                    <?= csrf_field() ?>
                    <button
                        type="submit"
                        class="service-toggle service-homepage-toggle <?= $isHomepage ? 'is-on' : '' ?>"
                        aria-pressed="<?= $isHomepage ? 'true' : 'false' ?>"
                        aria-label="<?= $isHomepage ? 'Remove' : 'Display' ?> <?= e($s['name']) ?> <?= $isHomepage ? 'from' : 'on' ?> homepage"
                        title="<?= $isActive ? ($isHomepage ? 'Hide from homepage' : 'Display on homepage') : 'Activate the service first' ?>"
                        <?= $isActive ? '' : 'disabled' ?>
                    >
                        <span class="service-toggle__track" aria-hidden="true"><span class="service-toggle__thumb"></span></span>
                        <span class="service-toggle__label"><?= $isHomepage ? 'Shown' : 'Hidden' ?></span>
                    </button>
                </form>
            </td>
            <td class="service-actions">
                <a href="<?= e($adminPath) ?>/services/<?= e((string) $s['id']) ?>/edit" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Edit
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
