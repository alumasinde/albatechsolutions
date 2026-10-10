<?php

use AlbaTech\Core\View; 
/**
 * @var string $pageTitle
 * @var string $content
 * @var string $csrf
 * @var string $error
 */
?>
<div class="login-wrap"><a class="login-brand" href="/"><span class="brand-mark">A</span><span><strong>AlbaTech Solutions</strong><small>Website administration</small></span></a>
    <section class="login-card">
        <div class="login-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h1>Welcome back</h1>
        <p>Sign in to manage your website and services.</p><?php if (!empty($error)): ?><div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> <?= View::escape($error) ?></div><?php endif; ?><form method="post" action="/admin/login" class="form-stack"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>"><label>Email address<input type="email" name="email" autocomplete="username" required autofocus value="<?= View::escape($_POST['email'] ?? '') ?>"></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="btn btn-primary btn-full" type="submit">Sign in <i class="fa-solid fa-arrow-right"></i></button></form>
        <div class="login-foot"><i class="fa-solid fa-lock"></i> Protected administrator access</div>
    </section>
    <p class="login-copy">&copy; <?= date('Y') ?> AlbaTech Solutions</p>
</div>