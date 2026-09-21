<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_MINUTES = 15;

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $cfg = get_config();
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_name($cfg['session_name'] ?? 'arboretum_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Returns the logged-in user's row, or null. */
function current_user(): ?array
{
    start_session();
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function current_role(): ?string
{
    $u = current_user();
    return $u['role'] ?? null;
}

/** Redirects to login.php if not logged in. Call at the top of every protected page. */
function require_login(): array
{
    $user = current_user();
    if (!$user) {
        $dest = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: login.php' . ($dest ? '?next=' . urlencode($dest) : ''));
        exit;
    }

    if (!empty($user['must_change_password']) && basename($_SERVER['SCRIPT_NAME']) !== 'account.php') {
        set_flash('notice', 'Set a new password before continuing.');
        header('Location: account.php');
        exit;
    }

    return $user;
}

/** Call require_login() first, then this with the roles allowed on the page. */
function require_role(array $allowed): array
{
    $user = require_login();
    if (!in_array($user['role'], $allowed, true)) {
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
    return $user;
}

function is_admin(): bool
{
    return current_role() === 'admin';
}

function can_edit(): bool
{
    return in_array(current_role(), ['admin', 'editor'], true);
}

function can_delete(): bool
{
    return current_role() === 'admin';
}

/**
 * Attempts to log a user in. Returns true on success, or a string error
 * message on failure.
 */
function attempt_login(string $username, string $password)
{
    start_session();
    $pdo = db();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Always run password_verify against something, even for an unknown
    // user, so response timing doesn't reveal whether the username exists.
    $hashToCheck = $user['password_hash'] ?? '$2y$10$invalidsaltinvalidsaltinvalidsaltinvalidsal';

    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        return 'This account is temporarily locked. Try again in a few minutes.';
    }

    $ok = password_verify($password, $hashToCheck);

    if (!$user || !$ok || !$user['active']) {
        if ($user) {
            $attempts = (int)$user['failed_attempts'] + 1;
            $lockUntil = null;
            if ($attempts >= LOGIN_MAX_ATTEMPTS) {
                $lockUntil = date('Y-m-d H:i:s', time() + LOGIN_LOCK_MINUTES * 60);
                $attempts = 0;
            }
            $upd = $pdo->prepare('UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?');
            $upd->execute([$attempts, $lockUntil, $user['id']]);
        }
        return 'Incorrect username or password.';
    }

    $pdo->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?')
        ->execute([$user['id']]);

    session_regenerate_id(true);
    unset($user['password_hash']);
    $_SESSION['user'] = $user;

    return true;
}

function logout(): void
{
    start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function refresh_current_user(): void
{
    $user = current_user();
    if (!$user) {
        return;
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $fresh = $stmt->fetch();
    if ($fresh) {
        unset($fresh['password_hash']);
        $_SESSION['user'] = $fresh;
    }
}
