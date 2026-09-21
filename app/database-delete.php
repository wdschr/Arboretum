<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('databases.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT name FROM databases_directory WHERE id = ?');
$stmt->execute([$id]);
$name = $stmt->fetchColumn();

if ($name) {
    db()->prepare('DELETE FROM databases_directory WHERE id = ?')->execute([$id]);
    log_action('delete', 'database', $name);
    set_flash('success', 'Deleted ' . $name . '.');
} else {
    set_flash('error', 'That database was already gone.');
}

redirect('databases.php');
