<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-envelope-open-text"></i> Contact Messages</h1>

<table class="table">
    <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th>Status</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($messages as $m): ?>
        <tr>
            <td>
                <strong><?= e($m['name']) ?></strong><br>
                <span style="color:#64748b;font-size:0.85rem;"><?= e($m['email']) ?><?= $m['phone'] ? ' · ' . e($m['phone']) : '' ?></span>
            </td>
            <td><?= e($m['subject'] ?? '—') ?></td>
            <td style="max-width:280px;white-space:normal;"><?= e($m['message']) ?></td>
            <td><?= e($m['created_at']) ?></td>
            <td><span class="badge <?= $m['status'] === 'new' ? 'badge-active' : 'badge-inactive' ?>"><?= e($m['status']) ?></span></td>
            <td>
                <?php if ($m['status'] === 'new'): ?>
                <form method="POST" action="<?= e($adminPath . '/contact-messages/' . $m['id'] . '/read') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">Mark Read</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($messages)): ?>
    <p style="color:#94a3b8;margin-top:16px;">No messages yet.</p>
<?php endif; ?>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
