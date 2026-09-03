<?php
use App\Core\Config;
$title='Assistant Session #'.(int)$session['id'];
ob_start();
?>
<div class="page-header"><div><a href="<?= e(Config::get('admin.path','/admin')) ?>/assistant/sessions" class="back-link">← Assistant sessions</a><span class="eyebrow">Assistant session #<?= (int)$session['id'] ?></span><h1><?= e($session['user_name']??'Guest') ?></h1><p><?= e($session['started_at']) ?> · <?= !empty($session['completed_at']) ? 'Handed off to AlbaTech' : 'Conversation open' ?></p></div></div>
<div class="assistant-admin-grid"><section class="card assistant-admin-conversation"><h2>Conversation</h2><?php foreach($messages as $m): ?><article class="assistant-admin-message assistant-admin-message--<?= e($m['direction']) ?>"><span><?= e(ucfirst($m['direction'])) ?></span><p><?= nl2br(e($m['message'])) ?></p><time><?= e($m['created_at']) ?></time></article><?php endforeach; ?></section><aside class="card"><h2>Service matches</h2><?php foreach($matches as $m): ?><div class="assistant-match"><strong><?= e($m['service_name']) ?></strong><span>Score <?= e((string)$m['score']) ?></span><small><?= e($m['reason']??'') ?></small></div><?php endforeach; ?><?php if(!$matches): ?><p class="muted">No service matches recorded.</p><?php endif; ?></aside></div>
<?php $adminContent=ob_get_clean(); require dirname(__DIR__).'/layout.php';
