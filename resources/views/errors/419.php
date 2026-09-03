<?php ob_start(); ?>
<div class="error-page">
    <h1>419</h1>
    <p>Your session has expired. Please refresh the page and try again.</p>
    <a href="/" class="btn btn-primary">Refresh</a>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
