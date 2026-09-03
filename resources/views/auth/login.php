<?php ob_start(); ?>
<div class="auth-wrapper">
    <form class="card auth-card" method="POST" action="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>" novalidate>
        <?= csrf_field() ?>
        <h1><i class="fa-solid fa-shield-halved"></i> Sign in</h1>

        <?php $errors = flash_errors(); ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                    <p><?= e($msg) ?></p>
                <?php endforeach; endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <p style="text-align:right;margin:-6px 0 12px;"><a href="/forgot-password" style="font-size:0.82rem;color:#64748b;">Forgot password?</a></p>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-label">Sign in</span>
        </button>

        <div class="auth-divider"><span>or</span></div>

        <a href="/auth/google/redirect" class="btn btn-google btn-block">
            <i class="fa-brands fa-google"></i> Sign in with Google
        </a>

        <p style="text-align:center;font-size:0.85rem;margin-top:16px;color:#64748b;">
            Don't have an account? <a href="/register">Create one</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
