<?php ob_start(); $adminPath=\App\Core\Config::get('admin.path','/admin'); $status=$currentStatus ?? ''; ?>
<div class="admin-page-heading">
    <div><span class="eyebrow">Digital assistance</span><h1>Customer notifications</h1><p>Email, SMS and WhatsApp delivery history, including retries.</p></div>
</div>
<div class="card table-card">
    <div class="table-scroll"><table class="table"><thead><tr><th>Channel</th><th>Event</th><th>Customer</th><th>Recipient</th><th>Status</th><th>Attempts</th><th>Created</th><th>Sent</th></tr></thead><tbody>
    <?php foreach($notifications as $n): ?>
        <tr>
            <td><strong><?= e(strtoupper((string)$n['channel'])) ?></strong></td>
            <td><strong><?= e(ucwords(str_replace('_',' ',$n['event']))) ?></strong><br><span class="muted-small"><?= e($n['request_number']) ?></span></td>
            <td><?= e($n['name']) ?></td>
            <td><?= e($n['recipient']) ?></td>
            <td><span class="status-badge status-<?= e($n['status']) ?>"><?= e(ucfirst($n['status'])) ?></span><?php if(!empty($n['provider_message'])): ?><br><span class="muted-small"><?= e($n['provider_message']) ?></span><?php endif; ?>
                <?php if($n['status']==='failed' && \App\Core\Auth::can('assistance.notifications.manage')): ?><form method="POST" action="<?= e($adminPath) ?>/assistance/notifications/<?= (int)$n['id'] ?>/retry" style="margin-top:.35rem"><?= csrf_field() ?><button class="btn btn-secondary btn-sm" type="submit">Retry</button></form><?php endif; ?>
            </td>
            <td><?= e((string)($n['attempt_count'] ?? 0)) ?></td>
            <td><?= e(date('d M Y, H:i',strtotime($n['created_at']))) ?></td>
            <td><?= $n['sent_at'] ? e(date('d M Y, H:i',strtotime($n['sent_at']))) : '—' ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(!$notifications): ?><tr><td colspan="8" class="empty-table">No assistance notifications have been recorded yet.</td></tr><?php endif; ?>
    </tbody></table></div>
</div>
<?php $adminContent=ob_get_clean(); require dirname(__DIR__).'/layout.php';
