<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
require_login();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid form token.';
    } else {
        $old = is_string($_POST['current_password'] ?? null) ? $_POST['current_password'] : '';
        $new = is_string($_POST['new_password'] ?? null) ? $_POST['new_password'] : '';
        $confirm = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        $username = current_user();
        $stmt = db()->prepare('SELECT password_hash FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $userRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$userRow || !password_verify($old, $userRow['password_hash'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($new) < 12 || $new === 'admin') {
            $error = 'Use a new password of at least 12 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
            $stmt->bind_param('ss', $hash, $username);
            $stmt->execute();
            $stmt->close();
            session_regenerate_id(true);
            $success = 'Password changed successfully.';
        }
    }
}
$pageTitle = 'Change Password | FABWEB';
$activePage = 'login.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content"><section class="section"><div class="container narrow"><div class="card"><h1>Change manager password</h1><p>For security, change the default assessment credentials only after your marker has finished testing.</p>
<?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="notice success"><?= h($success) ?></p><?php endif; ?>
<form method="post" action="change_password.php"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><div class="field"><label for="current_password">Current password</label><input type="password" id="current_password" name="current_password"></div><div class="field"><label for="new_password">New password (12+ characters)</label><input type="password" id="new_password" name="new_password"></div><div class="field"><label for="confirm_password">Confirm new password</label><input type="password" id="confirm_password" name="confirm_password"></div><button class="button" type="submit">Change password</button></form>
<p><a href="manage.php">Back to HR management</a></p></div></div></section></main>
<?php require __DIR__ . '/includes/footer.inc'; ?>
