<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/password_policy.php';

start_session();

$pdo = db();
$userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

if ($userCount > 0) {
    http_response_code(403);
    die('Setup has already run - there is at least one account already. Delete setup.php from the server. If you\'ve lost admin access, ask another admin to reset your password from the Users page, or reset it directly in the database.');
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $setupKey = post_string('setup_key');
    $username  = post_string('username');
    $password  = (string)($_POST['password'] ?? '');
    $confirm   = (string)($_POST['password_confirm'] ?? '');
    $expectedKey = get_config()['setup_key'] ?? '';

    if ($expectedKey === '' || $expectedKey === 'change-this-to-something-long-and-random') {
        $errors[] = 'Set a real setup_key in config.php first (see config.example.php).';
    } elseif (!hash_equals($expectedKey, $setupKey)) {
        $errors[] = 'That setup key is wrong.';
    }
    if ($username === '' || !preg_match('/^[a-zA-Z0-9_.-]{2,50}$/', $username)) {
        $errors[] = 'Username must be 2-50 characters: letters, numbers, dot, dash or underscore.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    $errors = array_merge($errors, password_policy_errors($password, $username));

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role, active) VALUES (?, ?, ?, 1)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), 'admin']);
        set_flash('success', 'Admin account created. Log in below, then delete setup.php from the server.');
        redirect('login.php');
    }
}
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Set up &middot; <?= esc(get_config()['site_name'] ?? 'Arboretum') ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap">
  <div class="auth-wrap">
    <div class="auth-card">
      <h1>First-time setup</h1>
      <p class="sub">Create the first admin account. This page refuses to run again once one account exists.</p>

      <?php foreach ($errors as $e): ?>
        <div class="flash error"><?= esc($e) ?></div>
      <?php endforeach; ?>

      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="field">
          <label for="setup_key">Setup key</label>
          <input type="password" id="setup_key" name="setup_key" autocomplete="off" required>
          <span class="hint">From the setup_key value in config.php.</span>
        </div>
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= esc($username) ?>" autocomplete="username" required>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="new-password" required minlength="<?= PASSWORD_MIN_LENGTH ?>">
          <span class="hint">At least <?= PASSWORD_MIN_LENGTH ?> characters. NCSC's advice: three random unrelated words beats a short word with a number stuck on the end.</span>
        </div>
        <div class="field">
          <label for="password_confirm">Confirm password</label>
          <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" required minlength="<?= PASSWORD_MIN_LENGTH ?>">
        </div>
        <button type="submit" class="btn btn-primary">Create admin account</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
