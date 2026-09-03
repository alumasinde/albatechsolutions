<?php ob_start(); ?>
<h1><i class="fa-solid fa-user-shield"></i> Edit Role: <?= e($role['name']) ?></h1>

<form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/roles/<?= e((string) $role['id']) ?>" class="card">
    <?= csrf_field() ?>

    <?php foreach ($permissionGroups as $module => $permissions): ?>
        <div class="form-section">
            <h2><?= e(ucfirst($module)) ?></h2>
            <?php foreach ($permissions as $permission): ?>
                <div class="checkbox-row">
                    <input
                        type="checkbox"
                        id="perm_<?= e((string) $permission['id']) ?>"
                        name="permission_ids[]"
                        value="<?= e((string) $permission['id']) ?>"
                        <?= in_array((int) $permission['id'], $assignedPermissionIds, true) ? 'checked' : '' ?>
                    >
                    <label for="perm_<?= e((string) $permission['id']) ?>" style="margin:0;font-weight:400;">
                        <?= e($permission['name']) ?>
                        <span style="color:#94a3b8;">— <?= e($permission['description'] ?? '') ?></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Save Permissions</button>
</form>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
