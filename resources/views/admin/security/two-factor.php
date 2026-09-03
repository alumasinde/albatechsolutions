<?php ob_start(); ?>
<h1><i class="fa-solid fa-key"></i> Two-Factor Authentication</h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($user['two_factor_enabled'])): ?>
    <div class="card" style="max-width:480px;">
        <p><i class="fa-solid fa-circle-check" style="color:#15803d;"></i> Two-factor authentication is <strong>enabled</strong> on your account.</p>
        <form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/security/2fa/disable">
            <?= csrf_field() ?>
            <label for="password">Enter your password to disable</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn btn-danger btn-block">Disable 2FA</button>
        </form>
    </div>

<?php elseif ($setupUri): ?>
    <div class="card" style="max-width:480px;">
        <p>Scan this QR code with your authenticator app (Google Authenticator, Authy, 1Password, etc.), then enter the 6-digit code it shows.</p>
        <div id="qr-code" data-uri="<?= e($setupUri) ?>" style="margin:16px 0;"></div>
        <p style="font-size:0.8rem;color:#64748b;">Or enter manually: <code><?= e($setupSecret) ?></code></p>

        <form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/security/2fa/confirm">
            <?= csrf_field() ?>
            <label for="code">6-digit code</label>
            <input type="text" id="code" name="code" inputmode="numeric" required autofocus>
            <button type="submit" class="btn btn-primary btn-block">Enable 2FA</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="<?= asset('js/v4/two-factor-setup.js') ?>"></script>

<?php else: ?>
    <div class="card" style="max-width:480px;">
        <p>Two-factor authentication is currently <strong>disabled</strong>. Enabling it requires a code from an authenticator app at every login, in addition to your password.</p>
        <form method="POST" action="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/security/2fa/start">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary btn-block">Set Up 2FA</button>
        </form>
    </div>
<?php endif; ?>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
