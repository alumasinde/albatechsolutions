<?php ob_start(); ?>
<h1><i class="fa-solid fa-users"></i> Users</h1>

<p><a href="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/users/create" class="btn btn-primary">
    <i class="fa-solid fa-plus"></i> New User
</a></p>

<table class="table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Roles</th><th>2FA</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role_names'] ?: '—') ?></td>
            <td><?= $u['two_factor_enabled'] ? '<i class="fa-solid fa-check" style="color:#15803d"></i>' : '—' ?></td>
            <td>
                <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                    <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
            </td>
            <td>
                <form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/users/<?= e((string) $u['id']) ?>/toggle-active" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
