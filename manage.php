<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    http_response_code(403);
    exit('Invalid form token. Please reload the manager page.');
  }
  $action = clean($_POST['action'] ?? '', 30);
  if ($action === 'change_status') {
    $id = filter_var($_POST['EOInumber'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $status = clean($_POST['status'] ?? '', 20);
    if (!$id || !in_array($status, ['New', 'Current', 'Final'], true)) {
      flash('error', 'A valid EOI number and status are required.');
    } else {
      $stmt = db()->prepare('UPDATE eoi SET status = ? WHERE EOInumber = ?');
      $stmt->bind_param('si', $status, $id);
      $stmt->execute();
      $updated = $stmt->affected_rows;
      $stmt->close();
      flash($updated > 0 ? 'success' : 'info', $updated > 0 ? "EOI #$id updated to $status." : 'No status change was needed, or that EOI was not found.');
    }
  } elseif ($action === 'delete_by_job') {
    $jobRef = strtoupper(clean($_POST['delete_ref'] ?? '', 10));
    $confirm = ($_POST['confirm_delete'] ?? '') === 'yes';
    if (!valid_job_ref($jobRef) || !$confirm) {
      flash('error', 'Enter a valid job reference and tick the deletion confirmation box.');
    } else {
      $stmt = db()->prepare('DELETE FROM eoi WHERE job_ref = ?');
      $stmt->bind_param('s', $jobRef);
      $stmt->execute();
      $deleted = $stmt->affected_rows;
      $stmt->close();
      flash('success', "$deleted EOI(s) deleted for job reference $jobRef.");
    }
  } else {
    flash('error', 'Unknown manager action.');
  }
  redirect('manage.php');
}

$jobRef = strtoupper(clean($_GET['job_ref'] ?? '', 10));
$firstName = clean($_GET['first_name'] ?? '', 40);
$lastName = clean($_GET['last_name'] ?? '', 40);
$sortOptions = [
  'EOInumber' => 'EOI number',
  'job_ref' => 'Job reference',
  'first_name' => 'First name',
  'last_name' => 'Last name',
  'status' => 'Status',
  'created_at' => 'Date submitted'
];
$sort = clean($_GET['sort'] ?? 'EOInumber', 30);
if (!array_key_exists($sort, $sortOptions)) {
  $sort = 'EOInumber';
}
$direction = strtoupper(clean($_GET['direction'] ?? 'DESC', 4));
if (!in_array($direction, ['ASC', 'DESC'], true)) {
  $direction = 'DESC';
}
$where = [];
$values = [];
if ($jobRef !== '') {
  $where[] = 'job_ref = ?';
  $values[] = $jobRef;
}
if ($firstName !== '') {
  $where[] = 'first_name LIKE ?';
  $values[] = '%' . $firstName . '%';
}
if ($lastName !== '') {
  $where[] = 'last_name LIKE ?';
  $values[] = '%' . $lastName . '%';
}
$sql = 'SELECT * FROM eoi';
if ($where) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}
// ORDER BY field/direction are from fixed allowlists only.
$sql .= ' ORDER BY `' . $sort . '` ' . $direction . ', `EOInumber` DESC';
$stmt = db()->prepare($sql);
if ($values) {
  $stmt->bind_param(str_repeat('s', count($values)), ...$values);
}
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$jobs = db()->query('SELECT job_ref,title FROM jobs ORDER BY job_ref')->fetch_all(MYSQLI_ASSOC);
$flashMessage = take_flash();
$pageTitle = 'Manage EOIs | FABWEB';
$activePage = 'login.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
  <section class="page-banner">
    <div class="container manager-title">
      <div>
        <p class="eyebrow">HR management</p>
        <h1>Manage expressions of interest</h1>
        <p>Signed in as <?= h(current_user()) ?> · Search, sort, update and delete applications. <a class="light-link" href="change_password.php">Change password</a></p>
      </div>
      <form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><button type="submit" class="button button-outline">Log out</button></form>
    </div>
  </section>
  <section class="section">
    <div class="container">
      <?php if ($flashMessage): ?><p class="notice <?= $flashMessage['type'] === 'error' ? 'error' : 'success' ?>" role="status"><?= h($flashMessage['message']) ?></p><?php endif; ?>
      <div class="manager-grid">
        <div class="card">
          <h2>Find EOIs</h2>
          <p>Leave all filters blank to list every application.</p>
          <form method="get" action="manage.php" class="filter-form">
            <div class="field"><label for="filter_ref">Job reference</label><input id="filter_ref" name="job_ref" list="job_refs" value="<?= h($jobRef) ?>" placeholder="FW1001"><datalist id="job_refs"><?php foreach ($jobs as $job): ?><option value="<?= h($job['job_ref']) ?>"><?= h($job['title']) ?></option><?php endforeach; ?></datalist></div>
            <div class="field"><label for="filter_first">Applicant first name</label><input id="filter_first" name="first_name" value="<?= h($firstName) ?>"></div>
            <div class="field"><label for="filter_last">Applicant last name</label><input id="filter_last" name="last_name" value="<?= h($lastName) ?>"></div>
            <div class="field"><label for="sort">Sort results by</label><select id="sort" name="sort"><?php foreach ($sortOptions as $key => $label): ?><option value="<?= h($key) ?>" <?= $sort === $key ? 'selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label for="direction">Sort direction</label><select id="direction" name="direction">
                <option value="ASC" <?= $direction === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                <option value="DESC" <?= $direction === 'DESC' ? 'selected' : '' ?>>Descending</option>
              </select></div>
            <div><button class="button" type="submit">Search and sort</button> <a href="manage.php">Show all</a></div>
          </form>
        </div>
        <div class="card">
          <h2>Update EOI status</h2>
          <form method="post" action="manage.php" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="change_status">
            <div class="field"><label for="eoi_number">EOI number</label><input id="eoi_number" name="EOInumber" inputmode="numeric" type="number" min="1" placeholder="e.g. 1"></div>
            <div class="field"><label for="new_status">New status</label><select id="new_status" name="status">
                <option value="New">New</option>
                <option value="Current">Current</option>
                <option value="Final">Final</option>
              </select></div>
            <button class="button" type="submit">Save status</button>
          </form>
        </div>
        <div class="card delete-card">
          <h2>Delete EOIs by job reference</h2>
          <p class="muted">This permanently deletes all expressions of interest for the entered job reference. Job postings are not deleted.</p>
          <form method="post" action="manage.php" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="delete_by_job">
            <div class="field"><label for="delete_ref">Job reference to delete</label><input id="delete_ref" name="delete_ref" placeholder="FW1001" maxlength="6"></div>
            <label class="check"><input type="checkbox" name="confirm_delete" value="yes">I understand this will permanently delete these EOIs.</label><button type="submit" class="button danger">Delete EOIs for this job</button>
          </form>
        </div>
      </div>
      <div class="table-heading">
        <h2>Application records</h2>
        <p><?= count($results) ?> result(s)</p>
      </div>
      <div class="table-scroll">
        <table>
          <caption>EOIs matching selected manager filters</caption>
          <thead>
            <tr>
              <th scope="col">EOI #</th>
              <th scope="col">Job ref</th>
              <th scope="col">First name</th>
              <th scope="col">Last name</th>
              <th scope="col">DOB</th>
              <th scope="col">Gender</th>
              <th scope="col">Address</th>
              <th scope="col">State</th>
              <th scope="col">Postcode</th>
              <th scope="col">Email</th>
              <th scope="col">Phone</th>
              <th scope="col">Skills</th>
              <th scope="col">Other skills</th>
              <th scope="col">Status</th>
              <th scope="col">Submitted</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $entry): ?><tr>
                <td><?= (int) $entry['EOInumber'] ?></td>
                <td><?= h($entry['job_ref']) ?></td>
                <td><?= h($entry['first_name']) ?></td>
                <td><?= h($entry['last_name']) ?></td>
                <td><?= h($entry['date_of_birth']) ?></td>
                <td><?= h($entry['gender']) ?></td>
                <td><?= h($entry['street_address']) ?>, <?= h($entry['suburb_town']) ?></td>
                <td><?= h($entry['state']) ?></td>
                <td><?= h($entry['postcode']) ?></td>
                <td><?= h($entry['email']) ?></td>
                <td><?= h($entry['phone']) ?></td>
                <td><?= h($entry['skills']) ?></td>
                <td><?= h($entry['other_skills']) ?></td>
                <td><span class="status-label"><?= h($entry['status']) ?></span></td>
                <td><?= h($entry['created_at']) ?></td>
              </tr><?php endforeach; ?>
            <?php if (!$results): ?><tr>
                <td colspan="15">No EOIs match these filters. Try Show all.</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <p class="muted">Tip: find an EOI number in the table, then enter it in the status form. To see everything, choose “Show all.”</p>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>