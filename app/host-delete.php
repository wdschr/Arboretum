<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_verify();

$id = get_int('id', 0) ?: (int)($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT hostname FROM hosts WHERE id = ?');
$stmt->execute([$id]);
$hostname = $stmt->fetchColumn();

if ($hostname) {
    db()->prepare('DELETE FROM hosts WHERE id = ?')->execute([$id]);
    log_action('delete', 'host', $hostname);
    set_flash('success', 'Deleted ' . $hostname . '.');
} else {
    set_flash('error', 'That hostname was already gone.');
}

redirect('index.php');
