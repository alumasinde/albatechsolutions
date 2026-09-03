<?php ob_start(); ?>
<h1><i class="fa-solid fa-user-plus"></i> New User</h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/users" class="card" style="max-width:520px;">
    <?= csrf_field() ?>

    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="phone">Phone</label>
    <input type="text" id="phone" name="phone">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required minlength="8">

    <label>Roles</label>
    <?php foreach ($roles as $role): ?>
        <div class="checkbox-row">
            <input type="checkbox" id="role_<?= e((string) $role['id']) ?>" name="role_ids[]" value="<?= e((string) $role['id']) ?>">
            <label for="role_<?= e((string) $role['id']) ?>" style="margin:0;font-weight:400;"><?= e($role['name']) ?></label>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary btn-block">Create User</button>
</form>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
