<?php $title='Notification preferences | AlbaTech'; ob_start(); $p=$preferences??[]; ?>
<section class="public-assistance-page">
<div class="public-assistance-card">
<span class="eyebrow">AlbaTech assistance</span>
<h1>Notification preferences</h1>
<p>Choose how you want AlbaTech to send updates about <strong><?= e($requestItem['request_number']) ?></strong>.</p>
<?php if($errors): ?><div class="alert alert-error"><?php foreach($errors as $fieldErrors): foreach($fieldErrors as $error): ?><p><?= e($error) ?></p><?php endforeach; endforeach; ?></div><?php endif; ?>
<?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<form method="POST" action="/request/<?= e($token) ?>/notifications">
<?= csrf_field() ?>
<label class="check-row"><input type="checkbox" name="email_notifications" value="1" <?= !empty($p['email_enabled'])?'checked':'' ?>> Email updates</label>
<label class="check-row"><input type="checkbox" name="sms_notifications" value="1" <?= !empty($p['sms_enabled'])?'checked':'' ?>> SMS updates</label>
<label class="check-row"><input type="checkbox" name="whatsapp_notifications" value="1" <?= !empty($p['whatsapp_enabled'])?'checked':'' ?>> WhatsApp updates</label>
<button class="btn btn-primary" type="submit">Save preferences</button>
</form>
<p class="muted-small">You can change these preferences later from this private link. AlbaTech will never ask you for your M-Pesa PIN, OTP or password.</p>
<a href="/request/<?= e($token) ?>">Back to request</a>
</div></section>
<?php $content=ob_get_clean(); require dirname(__DIR__).'/layout.php'; ?>
