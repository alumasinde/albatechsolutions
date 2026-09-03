<?php
use App\Core\Config;
$title='Digital Assistant Sessions';
ob_start();
?>
<div class="page-header"><div><span class="eyebrow">Assistant</span><h1>Digital Assistant Sessions</h1><p>Review conversations, service matches and handoffs from the public AlbaTech assistant.</p></div></div>
<div class="card table-card"><div class="table-responsive"><table class="data-table"><thead><tr><th>Started</th><th>Session</th><th>User</th><th>Messages</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($sessions as $s): ?><tr><td><?= e($s['started_at']) ?></td><td>#<?= (int)$s['id'] ?></td><td><?= e($s['user_name']??'Guest') ?></td><td><?= (int)$s['message_count'] ?></td><td><?= !empty($s['completed_at']) ? 'Handed off' : 'Open' ?></td><td><a class="btn btn-secondary btn-sm" href="<?= e(Config::get('admin.path','/admin')) ?>/assistant/sessions/<?= (int)$s['id'] ?>">View</a></td></tr><?php endforeach; ?>
<?php if(!$sessions): ?><tr><td colspan="6" class="empty-state">No assistant sessions yet.</td></tr><?php endif; ?></tbody></table></div></div>
<?php $adminContent=ob_get_clean(); require dirname(__DIR__).'/layout.php';
