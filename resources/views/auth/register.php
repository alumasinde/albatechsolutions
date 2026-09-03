<?php ob_start(); ?>
<div class="auth-wrapper">
    <form class="card auth-card" method="POST" action="/register" novalidate>
        <?= csrf_field() ?>
        <h1><i class="fa-solid fa-user-plus"></i> Create an account</h1>

        <?php $errors = flash_errors(); ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                    <p><?= e($msg) ?></p>
                <?php endforeach; endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= e(old('name')) ?>" required autofocus>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" required>

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= e(old('phone')) ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">

        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-label">Create Account</span>
        </button>

        <div class="auth-divider"><span>or</span></div>

        <a href="/auth/google/redirect" class="btn btn-google btn-block">
            <i class="fa-brands fa-google"></i> Sign up with Google
        </a>

        <p style="text-align:center;font-size:0.85rem;margin-top:16px;color:#64748b;">
            Already have an account? <a href="<?= e(\App\Core\Config::get('auth.login_path', '/login')) ?>">Sign in</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
