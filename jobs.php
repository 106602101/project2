<?php
require_once "settings.php";

$conn = mysqli_connect($host, $user, $pwd, $sql_db);
mysqli_set_charset($conn, "utf8mb4");

// Search
$search = trim($_GET["search"] ?? "");

if ($search != "") {
    // Search ref, title, summary and description
    $like = "%" . $search . "%";
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM jobs
         WHERE job_ref LIKE ? OR title LIKE ? OR summary LIKE ? OR description LIKE ?
         ORDER BY job_ref");
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Nothing searched will show every job
    $result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY job_ref");
}

$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);

function print_list_items($text) {
    foreach (explode("|", $text) as $item) {
        echo "<li>" . htmlspecialchars($item) . "</li>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Explore cybersecurity career opportunities at FABWEB Cybersecurity Services.">
  <title>Cybersecurity Jobs | FABWEB</title>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/layout.css">
</head>

<body>

  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">
        <img src="assets/logo.svg" alt="FABWEB shield logo">
        <span class="brand-text">
          FABWEB
          <small>Cybersecurity Services</small>
        </span>
      </a>
      <nav class="primary-nav" aria-label="Primary navigation">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="jobs.php" aria-current="page">Jobs</a></li>
          <li><a href="apply.php">Apply</a></li>
          <li><a href="about.php">About Our Team</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>

    <section class="page-banner">
      <div class="container">
        <p class="eyebrow">Open Positions</p>
        <h1>Cybersecurity Careers at FABWEB</h1>
        <p>
          Explore career opportunities across security operations,
          penetration testing, cloud security and cyber risk.
        </p>

        <!-- Search bar: sends ?search=... back to this page -->
        <form method="get" action="jobs.php">
          <label for="search">Search jobs</label>
          <input type="text" id="search" name="search"
                 placeholder="e.g. cloud, FW1002, penetration"
                 value="<?php echo htmlspecialchars($search); ?>">
          <input type="submit" value="Search">
          <?php if ($search != "") echo '<a href="jobs.php">Clear search</a>'; ?>
        </form>
      </div>
    </section>

    <section class="section">
      <div class="container job-layout">

        <div class="job-list">

          <?php if ($search != "") { ?>
            <p><?php echo count($jobs); ?> job(s) found for "<?php echo htmlspecialchars($search); ?>".</p>
          <?php } ?>

          <?php if (count($jobs) == 0) { ?>
            <p>No jobs match your search. <a href="jobs.php">Show all jobs</a></p>
          <?php } ?>

          <?php foreach ($jobs as $job) { ?>
          <section class="job-card" id="<?php echo strtolower($job["job_ref"]); ?>">

            <header>
              <p class="eyebrow">Reference <?php echo htmlspecialchars($job["job_ref"]); ?></p>
              <h2><?php echo htmlspecialchars($job["title"]); ?></h2>
              <p><?php echo htmlspecialchars($job["summary"]); ?></p>
            </header>

            <div class="job-body">

              <ul class="job-meta">
                <li><strong>Salary:</strong> <?php echo htmlspecialchars($job["salary"]); ?></li>
                <li><strong>Employment:</strong> <?php echo htmlspecialchars($job["employment_type"]); ?></li>
                <li><strong>Location:</strong> <?php echo htmlspecialchars($job["location"]); ?></li>
                <li><strong>Reports to:</strong> <?php echo htmlspecialchars($job["reports_to"]); ?></li>
              </ul>

              <p><?php echo htmlspecialchars($job["description"]); ?></p>

              <h3>Key Responsibilities</h3>
              <ol><?php print_list_items($job["responsibilities"]); ?></ol>

              <h3>Essential Requirements</h3>
              <ul><?php print_list_items($job["essential"]); ?></ul>

              <h3>Preferable Requirements</h3>
              <ul><?php print_list_items($job["preferable"]); ?></ul>

              <div class="button-row">
                <a class="button" href="apply.php">Apply for <?php echo htmlspecialchars($job["job_ref"]); ?></a>
              </div>

            </div>

          </section>
          <?php } ?>

        </div>

        <!-- Application Guide -->
        <aside class="job-aside" aria-labelledby="application-tips-title">
          <h2 id="application-tips-title">Application Tips</h2>
          <p>
            Use the six-character job reference shown in each
            position description when completing the application form.
          </p>
          <ul>
            <?php foreach ($jobs as $job) { ?>
              <li><strong><?php echo htmlspecialchars($job["job_ref"]); ?></strong> — <?php echo htmlspecialchars($job["title"]); ?></li>
            <?php } ?>
          </ul>
          <p>
            Review the essential and preferable requirements before
            submitting your application.
          </p>
          <a class="button" href="apply.php">Open Application Form</a>
        </aside>

      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <section>
          <h2>FABWEB Cybersecurity Services</h2>
          <p>Secure the Web. Protect What Matters.</p>
          <p>Melbourne, Victoria, Australia</p>
        </section>
        <section>
          <h3>Contact</h3>
          <ul>
            <li><a href="mailto:careers@fabweb.com.au">careers@fabweb.com.au</a></li>
          </ul>
        </section>
        <section>
          <h3>Project Links</h3>
          <ul>
            <li><a href="https://github.com/RShashimalKarunasinghe/Applied-Web-Project.git" target="_blank" rel="noopener">GitHub Repository</a></li>
            <li><a href="login.php">Manager Login</a></li>
          </ul>
        </section>
      </div>
    </div>
  </footer>

</body>
</html>