<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Clean form input for storage; escape HTML only when rendering with h(). */
function clean($value, int $maxLength = 1000): string
{
    if (!is_string($value)) {
        return '';
    }
    $value = trim(stripslashes($value));
    $value = strip_tags($value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $maxLength, 'UTF-8')
        : substr($value, 0, $maxLength);
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): bool
{
    return isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
        && hash_equals(csrf_token(), $_POST['csrf_token']);
}

function redirect(string $path): void
{
    header('Location: ' . $path, true, 303);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $result = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $result;
}

function current_user(): ?string
{
    return isset($_SESSION['admin_username']) ? (string) $_SESSION['admin_username'] : null;
}

function require_login(): void
{
    if (current_user() === null) {
        redirect('login.php');
    }
}

function valid_job_ref(string $value): bool
{
    return (bool) preg_match('/^[A-Za-z0-9]{5,6}$/D', $value);
}

function display_pipe_list(?string $items, string $listType = 'ul'): void
{
    $tag = $listType === 'ol' ? 'ol' : 'ul';
    echo '<' . $tag . '>';
    foreach (explode('|', (string) $items) as $item) {
        $item = trim($item);
        if ($item !== '') {
            echo '<li>' . h($item) . '</li>';
        }
    }
    echo '</' . $tag . '>';
}
