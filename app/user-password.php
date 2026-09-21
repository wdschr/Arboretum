<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$id]);
$username = $stmt->fetchColumn();

if (!$username) {
    set_flash('error', 'That user no longer exists.');
    redirect('users.php');
}

$tempPassword = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12);
db()->prepare('UPDATE users SET password_hash = ?, must_change_password = 1, failed_attempts = 0, locked_until = NULL WHERE id = ?')
    ->execute([password_hash($tempPassword, PASSWORD_DEFAULT), $id]);

log_action('update', 'user', $username, 'password reset by admin');
set_flash('success', 'New temporary password for ' . $username . ': ' . $tempPassword . ' - give this to them now, it will not be shown again. They must change it at next login.');
redirect('users.php');
