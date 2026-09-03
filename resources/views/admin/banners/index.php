<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-panorama"></i> Banners</h1>

<p><a href="<?= e($adminPath) ?>/banners/create" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Banner</a></p>

<table class="table">
    <thead><tr><th>Title</th><th>Placement</th><th>Status</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($banners as $b): ?>
        <tr>
            <td><?= e($b['title'] ?? '—') ?></td>
            <td><?= e($b['placement']) ?></td>
            <td><span class="badge <?= $b['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $b['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td><a href="<?= e($adminPath) ?>/banners/<?= e((string) $b['id']) ?>/edit">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
