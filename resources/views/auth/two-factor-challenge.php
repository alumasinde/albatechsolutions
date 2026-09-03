<?php ob_start(); ?>
<div class="auth-wrapper">
    <form class="card auth-card" method="POST" action="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>" novalidate>
        <?= csrf_field() ?>
        <h1><i class="fa-solid fa-key"></i> Verification code</h1>
        <p style="color:#64748b;font-size:0.9rem;margin:-8px 0 0;">
            Enter the 6-digit code from your authenticator app, or a recovery code.
        </p>

        <?php $errors = flash_errors(); ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                    <p><?= e($msg) ?></p>
                <?php endforeach; endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="code">Code</label>
        <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" required autofocus>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-label">Verify</span>
        </button>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
