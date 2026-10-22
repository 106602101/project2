<?php require_once "auth_check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="HR manager area for FABWEB Cybersecurity Services.">
  <title>Manage Applications | FABWEB Cybersecurity Services</title>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/layout.css">
</head>
<body>
  <main id="main-content">
    <section class="container">
      <h1>Manager Dashboard</h1>
      <p>Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
      <p><a class="button secondary" href="logout.php">Log out</a></p>
    </section>
  </main>
</body>
</html>
