<?php
require_once __DIR__ . '/functions.inc';
$pageTitle = $pageTitle ?? 'FABWEB Cybersecurity Services';
$activePage = $activePage ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="FABWEB Cybersecurity Services — security, trust and careers in Melbourne.">
  <title><?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php" aria-label="FABWEB Home">
      <img src="images/logo.svg" alt="" width="46" height="46">
      <span>FABWEB<small>Cybersecurity Services</small></span>
    </a>
    <?php require __DIR__ . '/nav.inc'; ?>
  </div>
</header>