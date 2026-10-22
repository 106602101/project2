
<?php
$pageTitle = $pageTitle ?? 'FABWEB Cybersecurity Services';
$pageDescription = $pageDescription
    ?? 'FABWEB Cybersecurity Services recruitment website.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="description"
          content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/layout.css">

    <?php if (!empty($pageStyles)): ?>
        <style><?= $pageStyles ?></style>
    <?php endif; ?>
</head>
<body>

<a class="skip-link" href="#main-content">
    Skip to main content
</a>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php"
           aria-label="FABWEB Cybersecurity Services home">

            <img src="assets/logo.svg"
                 alt="FABWEB shield logo">

            <span class="brand-text">
                FABWEB
                <small>Cybersecurity Services</small>
            </span>
        </a>

        <?php require __DIR__ . '/nav.inc'; ?>
    </div>
</header>
