<?php ob_start(); ?>
<div class="auth-wrapper">
    <form class="card auth-card" method="POST" action="/reset-password" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <h1><i class="fa-solid fa-lock"></i> Reset password</h1>

        <?php $errors = flash_errors(); ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                    <p><?= e($msg) ?></p>
                <?php endforeach; endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="password">New password</label>
        <input type="password" id="password" name="password" minlength="8" required autofocus>

        <label for="password_confirmation">Confirm new password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-label">Reset password</span>
        </button>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
