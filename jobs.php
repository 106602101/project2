<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
$search = clean($_GET['search'] ?? '', 80);
if ($search !== '') {
  $like = '%' . $search . '%';
  $stmt = db()->prepare('SELECT * FROM jobs WHERE is_open = 1 AND (job_ref LIKE ? OR title LIKE ? OR summary LIKE ? OR description LIKE ? OR responsibilities LIKE ?) ORDER BY job_ref');
  $stmt->bind_param('sssss', $like, $like, $like, $like, $like);
  $stmt->execute();
  $jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
} else {
  $jobs = db()->query('SELECT * FROM jobs WHERE is_open = 1 ORDER BY job_ref')->fetch_all(MYSQLI_ASSOC);
}
$pageTitle = 'Open Cybersecurity Jobs | FABWEB';
$activePage = 'jobs.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
  <section class="page-banner">
    <div class="container">
      <p class="eyebrow">Open positions</p>
      <h1>Cybersecurity careers at FABWEB</h1>
      <p>Discover opportunities in security operations, testing, cloud engineering and risk management.</p>
      <form class="search-form" method="get" action="jobs.php" role="search">
        <div><label for="search">Search available positions</label><input id="search" name="search" type="search" placeholder="e.g. cloud, FW1002, testing" value="<?= h($search) ?>"></div>
        <button class="button" type="submit">Search jobs</button><?php if ($search !== ''): ?><a class="text-link" href="jobs.php">Clear</a><?php endif; ?>
      </form>
    </div>
  </section>
  <section class="section">
    <div class="container job-layout">
      <div>
        <p class="result-count"><?= count($jobs) ?> position(s)<?= $search !== '' ? ' matching “' . h($search) . '”' : ' available' ?></p>
        <?php if (!$jobs): ?><div class="card">
            <h2>No results found</h2>
            <p>Try another keyword or <a href="jobs.php">browse all positions</a>.</p>
          </div><?php endif; ?>
        <?php foreach ($jobs as $job): ?>
          <article class="card job-card" id="<?= h(strtolower($job['job_ref'])) ?>">
            <p class="eyebrow dark">Reference <?= h($job['job_ref']) ?></p>
            <h2><?= h($job['title']) ?></h2>
            <p><?= h($job['summary']) ?></p>
            <dl class="job-meta">
              <div>
                <dt>Salary</dt>
                <dd><?= h($job['salary']) ?></dd>
              </div>
              <div>
                <dt>Employment</dt>
                <dd><?= h($job['employment_type']) ?></dd>
              </div>
              <div>
                <dt>Location</dt>
                <dd><?= h($job['location']) ?></dd>
              </div>
              <div>
                <dt>Reports to</dt>
                <dd><?= h($job['reports_to']) ?></dd>
              </div>
            </dl>
            <p><?= h($job['description']) ?></p>
            <h3>Key responsibilities</h3><?php display_pipe_list($job['responsibilities'], 'ol'); ?>
            <h3>Essential requirements</h3><?php display_pipe_list($job['essential']); ?>
            <h3>Preferable requirements</h3><?php display_pipe_list($job['preferable']); ?>
            <a class="button" href="apply.php?job_ref=<?= rawurlencode($job['job_ref']) ?>">Apply for <?= h($job['job_ref']) ?></a>
          </article>
        <?php endforeach; ?>
      </div>
      <aside class="card aside">
        <h2>Application guide</h2>
        <p>Use the reference code displayed beside each advertised job when applying.</p>
        <p>All application data is validated on the server before it is saved.</p><a class="button" href="apply.php">Open application</a>
      </aside>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>