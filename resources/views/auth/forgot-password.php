<?php ob_start(); ?>
<div class="auth-wrapper">
    <form class="card auth-card" method="POST" action="/forgot-password" novalidate>
        <?= csrf_field() ?>
        <h1><i class="fa-solid fa-key"></i> Forgot password</h1>
        <p style="color:#64748b;font-size:0.9rem;margin:-8px 0 16px;">
            Enter your account email and we'll send you a link to reset your password.
        </p>

        <?php $success = \App\Core\Session::getFlash('_success'); ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

        <?php $errors = flash_errors(); ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                    <p><?= e($msg) ?></p>
                <?php endforeach; endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autofocus>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-label">Send reset link</span>
        </button>

        <p style="text-align:center;font-size:0.85rem;margin-top:16px;color:#64748b;">
            <a href="<?= e(\App\Core\Config::get('auth.login_path', '/login')) ?>">Back to sign in</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
