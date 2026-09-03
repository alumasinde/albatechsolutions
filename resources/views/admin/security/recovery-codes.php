<?php ob_start(); ?>
<h1><i class="fa-solid fa-circle-check" style="color:#15803d;"></i> Two-Factor Authentication Enabled</h1>

<div class="card" style="max-width:520px;">
    <p><strong>Save these recovery codes somewhere safe.</strong> Each one can be used once to log in if you lose access to your authenticator app. They won't be shown again.</p>

    <div class="recovery-codes">
        <?php foreach ($codes as $code): ?>
            <div><?= e($code) ?></div>
        <?php endforeach; ?>
    </div>

    <a href="<?= e(\App\Core\Config::get('admin.path', '/admin')) ?>/security/2fa" class="btn btn-primary btn-block" style="margin-top:20px;">
        Done
    </a>
</div>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
