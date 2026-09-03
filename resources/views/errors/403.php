<?php ob_start(); ?>
<div class="error-page">
    <h1>403</h1>
    <p>You don't have permission to access this page.</p>
    <a href="/dashboard" class="btn btn-primary">Back to dashboard</a>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
