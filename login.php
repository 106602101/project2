<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
if (current_user() !== null) { redirect('manage.php'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'The form expired. Reload the page and try again.';
    } else {
        $now = time();
        $attempts = array_filter($_SESSION['login_attempts'] ?? [], static fn($t) => is_int($t) && $t > $now - 600);
        if (count($attempts) >= 5) {
            $error = 'Too many attempts. Try again after 10 minutes.';
        } else {
            $username = clean($_POST['username'] ?? '', 60);
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $stmt = db()->prepare('SELECT username,password_hash FROM users WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row && password_verify($password, $row['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['login_attempts'] = [];
                unset($_SESSION['csrf_token']);
                redirect('manage.php');
            }
            $attempts[] = $now;
            $_SESSION['login_attempts'] = array_values($attempts);
            $error = 'Incorrect username or password.';
        }
    }
}
$pageTitle = 'HR Manager Login | FABWEB';
$activePage = 'login.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content"><section class="section"><div class="container narrow"><div class="card auth-card"><p class="eyebrow dark">Restricted area</p><h1>HR Manager Login</h1><p>Sign in to manage expressions of interest.</p>
<?php if ($error): ?><p class="notice error" role="alert"><?= h($error) ?></p><?php endif; ?>
<form method="post" action="login.php" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><div class="field"><label for="username">Username</label><input type="text" name="username" id="username" autocomplete="username"></div><div class="field"><label for="password">Password</label><input type="password" name="password" id="password" autocomplete="current-password"></div><button class="button" type="submit">Sign in</button></form>
</div></div></section></main>
<?php require __DIR__ . '/includes/footer.inc'; ?>
