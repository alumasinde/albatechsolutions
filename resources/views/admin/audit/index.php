<?php ob_start(); ?>
<h1><i class="fa-solid fa-clock-rotate-left"></i> Audit Log</h1>

<table class="table">
    <thead><tr><th>When</th><th>User</th><th>Action</th><th>Entity</th><th>IP</th></tr></thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= e($log['created_at']) ?></td>
            <td><?= e($log['user_name'] ?? 'System') ?></td>
            <td><?= e($log['action']) ?></td>
            <td><?= e(trim(($log['entity_type'] ?? '') . ' #' . ($log['entity_id'] ?? ''))) ?></td>
            <td><?= e($log['ip_address'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p style="color:#64748b;font-size:0.85rem;margin-top:16px;">
    Showing page <?= (int) $page ?> — <?= (int) $total ?> total entries.
</p>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
