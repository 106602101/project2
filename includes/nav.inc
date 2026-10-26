<nav class="primary-nav" aria-label="Primary navigation">
  <ul>
  <?php foreach (['index.php' => 'Home', 'jobs.php' => 'Jobs', 'apply.php' => 'Apply', 'about.php' => 'About', 'login.php' => 'Manage'] as $path => $label): ?>
    <li><a href="<?= h($path) ?>" <?= $activePage === $path ? 'aria-current="page"' : '' ?>><?= h($label) ?></a></li>
  <?php endforeach; ?>
  </ul>
</nav>