<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-file-lines"></i> Pages</h1>

<p><a href="<?= e($adminPath) ?>/pages/create" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Page</a></p>

<table class="table">
    <thead><tr><th>Title</th><th>Type</th><th>Search topic</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($pages as $p): ?>
        <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="badge badge-active"><?= e(str_replace('_', ' ', $p['page_type'] ?? 'general')) ?></span></td>
            <td><?= e($p['focus_keyword'] ?? '—') ?></td>
            <td><span class="badge <?= $p['status'] === 'published' ? 'badge-active' : 'badge-inactive' ?>"><?= e($p['status']) ?></span></td>
            <td><?= e($p['updated_at']) ?></td>
            <td>
                <a href="<?= e($adminPath) ?>/pages/<?= e((string) $p['id']) ?>/edit">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
