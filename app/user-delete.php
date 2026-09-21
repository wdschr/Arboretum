<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

$me = require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);

if ($id === (int)$me['id']) {
    set_flash('error', "You can't delete your own account. Ask another admin.");
    redirect('users.php');
}

$stmt = db()->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$id]);
$username = $stmt->fetchColumn();

if ($username) {
    db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    log_action('delete', 'user', $username);
    set_flash('success', 'Deleted ' . $username . '.');
} else {
    set_flash('error', 'That user was already gone.');
}

redirect('users.php');
