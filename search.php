<?php
/** Backwards-compatible search endpoint; search is performed by jobs.php with SQL. */
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.inc';
$search = clean($_GET['search'] ?? '', 80);
redirect('jobs.php' . ($search !== '' ? '?search=' . rawurlencode($search) : ''));
