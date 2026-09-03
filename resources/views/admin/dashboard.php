<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
ob_start();
?>
<div class="dashboard-header">
    <div>
        <h1>Welcome back, <?= e(explode(' ', $user['name'] ?? 'there')[0]) ?></h1>
        <p class="dashboard-subtitle">Here's what's happening with your site today.</p>
    </div>
    <form method="POST" action="/logout">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </button>
    </form>
</div>

<?php if (!$twoFactorEnabled): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <span><i class="fa-solid fa-triangle-exclamation"></i> Two-factor authentication isn't enabled on your account yet.</span>
    <a href="<?= e($adminPath) ?>/security/2fa" class="btn btn-primary" style="white-space:nowrap;">Set Up 2FA</a>
</div>
<?php endif; ?>

<?php if (empty($user['email_verified_at'])): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <span><i class="fa-solid fa-envelope-circle-check"></i> Please verify your email address — check your inbox for the link.</span>
    <form method="POST" action="/resend-verification" style="margin:0;">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary btn-sm" style="white-space:nowrap;">Resend email</button>
    </form>
</div>
<?php endif; ?>



<?php if (!empty($stats)): ?>
<div class="stat-grid">
    <?php foreach ($stats as $stat): ?>
        <a href="<?= e($adminPath . '/' . $stat['url']) ?>" class="stat-card">
            <div class="stat-icon"><i class="fa-solid <?= e($stat['icon']) ?>"></i></div>
            <div>
                <div class="stat-value"><?= (int) $stat['value'] ?></div>
                <div class="stat-label"><?= e($stat['label']) ?></div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="dashboard-columns">
    <div class="card">
        <h2 style="margin-top:0;">Quick Actions</h2>
        <div class="quick-actions">
            <?php if (\App\Core\Auth::hasRole('customer')): ?>
                <a href="/services" class="quick-action"><i class="fa-solid fa-briefcase"></i> Browse Services</a>
            <?php endif; ?>
            <?php if (\App\Core\Auth::can('pages.manage')): ?>
                <a href="<?= e($adminPath) ?>/pages/create" class="quick-action"><i class="fa-solid fa-file-circle-plus"></i> New Page</a>
            <?php endif; ?>
            <?php if (\App\Core\Auth::can('blog.manage')): ?>
                <a href="<?= e($adminPath) ?>/blog/create" class="quick-action"><i class="fa-solid fa-pen"></i> New Blog Post</a>
            <?php endif; ?>
            <?php if (\App\Core\Auth::can('banners.manage')): ?>
                <a href="<?= e($adminPath) ?>/banners/create" class="quick-action"><i class="fa-solid fa-panorama"></i> New Banner</a>
            <?php endif; ?>
            <?php if (\App\Core\Auth::can('users.manage')): ?>
                <a href="<?= e($adminPath) ?>/users/create" class="quick-action"><i class="fa-solid fa-user-plus"></i> New User</a>
            <?php endif; ?>
            <?php if (\App\Core\Auth::can('settings.manage')): ?>
                <a href="<?= e($adminPath) ?>/settings" class="quick-action"><i class="fa-solid fa-palette"></i> Site Settings</a>
            <?php endif; ?>
            <a href="/" target="_blank" rel="noopener" class="quick-action"><i class="fa-solid fa-up-right-from-square"></i> View Site</a>
        </div>
    </div>

    <?php if (!empty($recentActivity)): ?>
    <div class="card">
        <h2 style="margin-top:0;">Recent Activity</h2>
        <ul class="activity-list">
            <?php foreach ($recentActivity as $log): ?>
                <li>
                    <span class="activity-action"><?= e(str_replace('_', ' ', $log['action'])) ?></span>
                    <span class="activity-meta"><?= e($log['user_name'] ?? 'System') ?> · <?= e($log['created_at']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="<?= e($adminPath) ?>/audit" style="font-size:0.85rem;">View full audit log →</a>
    </div>
    <?php endif; ?>
</div>
<?php
$adminContent = ob_get_clean();
require __DIR__ . '/layout.php';
