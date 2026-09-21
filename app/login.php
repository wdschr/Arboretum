<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

if (is_logged_in()) {
    redirect('index.php');
}

/** Only allow redirecting back to a plain local .php page, never off-site. */
function safe_next(string $next): string
{
    if ($next !== '' && preg_match('/^[a-zA-Z0-9_\-]+\.php(\?[a-zA-Z0-9=&_\-%.]*)?$/', ltrim($next, '/'))) {
        return ltrim($next, '/');
    }
    return 'index.php';
}

$next = safe_next((string)($_GET['next'] ?? $_POST['next'] ?? ''));
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = post_string('username');
    $password = (string)($_POST['password'] ?? '');

    $result = attempt_login($username, $password);
    if ($result === true) {
        redirect($next);
    }
    $error = $result;
}

$siteName = get_config()['site_name'] ?? 'Arboretum';
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= esc($siteName) ?> &middot; Log in</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="mark">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 21v-4"/><path d="M12 17 6.5 13h2.6L4.8 9.4h2.9L12 3l4.3 6.4h2.9L14.9 13h2.6L12 17Z"/>
        </svg>
        <?= esc($siteName) ?>
      </div>
      <h1>Log in</h1>

      <?php foreach (take_flashes() as $f): ?>
        <div class="flash <?= esc($f['type']) ?>"><?= esc($f['message']) ?></div>
      <?php endforeach; ?>
      <?php if ($error): ?>
        <div class="flash error"><?= esc($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= esc($next) ?>">
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= esc($username) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary">Log in</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
