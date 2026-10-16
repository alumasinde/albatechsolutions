<?php

use AlbaTech\Core\View; 

/**
 * @var string $pageTitle
 * @var string $content
 */
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= View::escape($pageTitle ?? 'Admin sign in') ?> | AlbaTech</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23155eef'/%3E%3Ctext x='16' y='23' font-family='Arial' font-size='20' font-weight='700' text-anchor='middle' fill='%23fff'%3EA%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= View::escape(View::asset('vendor/fontawesome/css/all.min.css')) ?>">
     <link rel="stylesheet" href="<?= View::escape(View::asset('css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= View::escape(View::asset('css/admin.css')) ?>">

</head>

<body class="login-body"><?= $content ?></body>

</html>