<?php $title='My Profile | AlbaTech'; ob_start(); $prefs=$notificationPreferences??[]; ?>
<section class="customer-page-head"><span class="eyebrow">Account</span><h1>Your profile</h1><p>Keep your contact details and notification preferences up to date.</p></section>
<div class="card customer-form-card">
<?php if($errors): ?><div class="alert alert-error"><?php foreach($errors as $fieldErrors): foreach($fieldErrors as $error): ?><p><?= e($error) ?></p><?php endforeach; endforeach; ?></div><?php endif; ?>
<?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<form method="POST" action="/account/profile">
<?= csrf_field() ?>
<label for="name">Full name</label><input id="name" name="name" value="<?= e($user['name']??'') ?>" required maxlength="150">
<label for="email">Email</label><input id="email" value="<?= e($user['email']??'') ?>" disabled>
<label for="phone">Phone</label><input id="phone" name="phone" value="<?= e($user['phone']??'') ?>" maxlength="20">
<h2>Notifications</h2>
<p class="muted-small">These preferences apply to future AlbaTech assistance requests. You can also change preferences for an individual request from its private portal.</p>
<label class="check-row"><input type="checkbox" name="email_notifications" value="1" <?= !empty($prefs['email_enabled'])?'checked':'' ?>> Email updates</label>
<label class="check-row"><input type="checkbox" name="sms_notifications" value="1" <?= !empty($prefs['sms_enabled'])?'checked':'' ?>> SMS updates</label>
<label class="check-row"><input type="checkbox" name="whatsapp_notifications" value="1" <?= !empty($prefs['whatsapp_enabled'])?'checked':'' ?>> WhatsApp updates</label>
<button class="btn btn-primary" type="submit">Save changes</button>
</form></div>
<?php $customerContent=ob_get_clean(); require __DIR__.'/layout.php'; ?>
