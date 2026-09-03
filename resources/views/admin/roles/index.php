<?php ob_start(); ?>
<h1><i class="fa-solid fa-user-shield"></i> Roles</h1>

<table class="table">
    <thead><tr><th>Name</th><th>Description</th><th>Permissions</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
        <tr>
            <td><?= e($role['name']) ?></td>
            <td><?= e($role['description'] ?? '') ?></td>
            <td><?= (int) $role['permission_count'] ?></td>
            <td><a href="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/roles/<?= e((string) $role['id']) ?>/edit">Edit permissions</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
