
<?php
$currentPage = basename(
    $_SERVER['SCRIPT_NAME'] ?? 'index.php'
);

$navigation = [
    'index.php' => 'Home',
    'jobs.php'  => 'Jobs',
    'apply.php' => 'Apply',
    'about.php' => 'About Our Team'
];
?>

<nav class="primary-nav" aria-label="Primary navigation">
    <ul>
        <?php foreach ($navigation as $file => $label): ?>
            <li>
                <a href="<?= htmlspecialchars($file, ENT_QUOTES, 'UTF-8') ?>"
                   <?= $currentPage === $file ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
